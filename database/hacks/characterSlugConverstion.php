$characters = Character::all();
foreach ($characters as $character) {
  $character->update(['slug' => slug($character->name)]);
}