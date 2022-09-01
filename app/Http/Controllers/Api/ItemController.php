<?php
namespace App\Http\Controllers\Api;

use App\{Character, Guild, Item};
use Auth;
use Illuminate\Support\Facades\{App, Validator, View};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ItemController extends \App\Http\Controllers\Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    private function getValidationRules() {
        return [
            'expansion_id' => 'integer|min:1|max:99',
            'faction'      => ['nullable', 'string', Rule::in(array_keys(Guild::getFactions()))],
            'query'        => 'string|min:1|max:40',
            'locale'       => ['nullable', 'string', Rule::in([
                    "de",
                    "en",
                    "es",
                    "fr",
                    "it",
                    "pt",
                    "ru",
                    "ko",
                    "cn",
                ])
            ],
        ];
    }

    /**
     * Lookup items similar to a given query.
     *
     * @param $expansionId integer
     * @param $query The name to search for.
     * @return \Illuminate\Http\Response
     */
    public function query($expansionId, $query)
    {
        $faction = request()->input('faction');
        $locale = request()->input('locale');

        $validator = Validator::make([
            'faction'      => $faction,
            'expansion_id' => $expansionId,
            'query'        => $query,
            'locale'       => $locale,
        ], $this->getValidationRules());

        $expansionId = (int) $expansionId;

        if ($validator->fails()) {
            return response()->json([
                    'error' => __("Query must be between 1 and :charLimit characters. Expansion ID must be between 1 and :expansionLimit. Faction must be 'a' or 'h'.", ['charLimit' => 40, 'expansionLimit' => 3])
                ],
                403);
        } else {
            if ($query && $query != " ") {
                $sqlQuery = Item::orderByDesc('weight')
                    ->limit(15);

                $selectFields = ['name', 'item_id', 'quality', 'item_level', 'faction', 'is_heroic', 'expansion_id'];

                if ($locale) {
                    array_push($selectFields, "name_{$locale}");
                    $sqlQuery = $sqlQuery->select(array_values($selectFields))
                    ->where([
                        ["name_{$locale}", 'like', '%' . trim($query) . '%'],
                        ['expansion_id', $expansionId],
                    ])
                    ->orWhere([
                        ['name', 'like', '%' . trim($query) . '%'],
                        ['expansion_id', $expansionId],
                    ])
                    ->orderBy("name_{$locale}")
                    ->orderBy('name');
                } else {
                    $sqlQuery = $sqlQuery->select($selectFields)
                    ->where([
                        ['name', 'like', '%' . trim($query) . '%'],
                        ['expansion_id', $expansionId],
                    ])
                    ->orderBy('name');
                    // For a more performant/powerful query...
                    // ->whereRaw(
                    //     "MATCH(`items`.`name`) AGAINST(? IN BOOLEAN MODE)",
                    //     ['+' . $query . ''] // note the prefixed or suffixed character(s) https://dev.mysql.com/doc/refman/5.5/en/fulltext-boolean.html
                    // )
                }

                if ($faction) {
                    $sqlQuery->ofFaction($faction);
                }

                $results = $sqlQuery->get();

                // For testing the query time:
                // $start = microtime(true);
                // $results = $results->get();
                // $end = microtime(true);
                // Log::debug($query . " (FULLTEXT): " . round(($end - $start) * 1000, 3) . "ms");

                // We just want the names in a plain old array; not key:value.
                $results = $results->transform(function ($item) use ($expansionId, $locale) {
                    $resultItem = ['value' => $item['item_id']];

                    $label = '';

                    if ($locale) {
                        $item->name = ($item["name_{$locale}"] ? $item["name_{$locale}"] : $item->name);
                    }

                    // Create a wowhead link
                    $label = (string)View::make('partials.item', [
                        'item'     => $item,
                        // 'iconSize' => 'tiny',
                        'wowheadLocale' => $locale,
                        'displayOnly' => true,
                        'fontWeight' => 'normal',
                    ]);

                    if ($item->is_heroic) {
                        $label = $label . ' <span class="smaller text-success">Heroic</span>';
                    }
                    if ($item->faction) {
                        if ($item->faction == Guild::FACTION_BEST) {
                            $label = $label . ' <span class="smaller text-horde">' . Character::FACTION_BEST . '</span>';
                        } else if ($item->faction == Guild::FACTION_WORST) {
                            $label = $label . ' <span class="smaller text-alliance">' . Character::FACTION_WORST . '</span>';
                        }
                    }
                    if ($item->item_level) {
                        $label = $label . ' <span class="smaller text-muted">ilvl ' . $item->item_level . '</span>';
                    }

                    $resultItem['label'] = '<span>' . $label . '</span>';
                    return $resultItem;
                });
            } else {
                return response()->json([], 200);
            }

            return response()->json($results, 200);
        }
    }
}
