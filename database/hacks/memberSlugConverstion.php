$members = Member::all();
foreach ($members as $member) {
  $member->update(['slug' => slug($member->username)]);
}