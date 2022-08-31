<?php
namespace App\Http\Controllers\Api;

use App\{Character, Guild, Item};
use Auth;
use Illuminate\Support\Facades\Validator;
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
     * @param $faction string 'a' or 'f'; see Guild for reference
     * @param $expansionId integer
     * @param $query The name to search for.
     * @return \Illuminate\Http\Response
     */
    public function query($faction, $expansionId, $query)
    {
        $locale = request()->input('locale');

        $validator = Validator::make([
            'faction'      => $faction,
            'expansion_id' => $expansionId,
            'query'        => $query,
            'locale'       => $locale,
        ], $this->getValidationRules());

        if ($validator->fails()) {
            return response()->json([
                    'error' => __("Query must be between 1 and :charLimit characters. Expansion ID must be between 1 and :expansionLimit. Faction must be 'a' or 'h'.", ['charLimit' => 40, 'expansionLimit' => 3])
                ],
                403);
        } else {
            if ($query && $query != " ") {
                $sqlQuery = Item::orderByDesc('weight')
                    ->limit(15);

                $selectFields = ['name', 'item_id', 'quality', 'faction', 'is_heroic'];

                if ($locale) {
                    $sqlQuery = $sqlQuery->select(array_push($selectFields, "name_{$locale}"))
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
                $results = $results->transform(function ($item) use ($locale) {
                    $resultItem = ['value' => $item['item_id']];

                    $label = null;
                    if ($locale) {
                        $label = ($item["name_{$locale}"] ? $item["name_{$locale}"] : $item->name);
                    } else {
                        $label = $item->name;
                    }
                    if ($item->is_heroic) {
                        $label = $label . ' (heroic)';
                    }
                    if ($item->faction) {
                        if ($item->faction == Guild::FACTION_BEST) {
                            $label = $label . ' (' . Character::FACTION_BEST . ')';
                        } else if ($item->faction == Guild::FACTION_WORST) {
                            $label = $label . ' (' . Character::FACTION_WORST . ')';
                        }
                    }

                    $resultItem['label'] = $label;
                    return $resultItem;
                });
            } else {
                return response()->json([], 200);
            }

            return response()->json($results, 200);
        }
    }
}
