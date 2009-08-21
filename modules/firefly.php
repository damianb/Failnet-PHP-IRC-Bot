<?php
$failnet->modules[] = 'firefly';
$loaded['firefly'] = true;

$help['firefly'] = 'Enter in ' . failnet::X02 . '|firefly ep (episode number)' . failnet::X02 . ' to look up a specified Firefly episode\'s info.
Enter in ' . failnet::X02 . '|firefly' . failnet::X02 . ' to lookup a random Firefly episode.';

function firefly()
{
	return rand(1, 14);
}

function episode($number)
{
	static $episodes;
	if(empty($episodes) || !is_array($episodes))
	{
		$episodes = array('Episode 1 - ' . failnet::X02 . 'Serenity' . failnet::X02 . ' - Malcolm Reynolds is a veteran and the captain of Serenity. He and his crew are smuggling goods, but they need to pick up some passengers for extra money. However, not all the passengers are what they seem. ',
			'Episode 2 - ' . failnet::X02 . 'The Train Job' . failnet::X02 . ' - The crew of Serenity takes on a train heist commissioned by a crime lord. They steal the goods, only to find it is medicine that is desperately needed by the town. ',
			'Episode 3 - ' . failnet::X02 . 'Bushwhacked' . failnet::X02 . ' - Serenity is pulled in by an Alliance cruiser while investigating a spaceship that was attacked by Reavers. Simon and River must hide to prevent capture, while something is wrong with the lone survivor of the attacked spaceship. ',
			'Episode 4 - ' . failnet::X02 . 'Shindig' . failnet::X02 . ' - Inara attends a formal society dance, only to find Malcolm there as well, attempting to set up a smuggling job. Mal comes to blows with Inara\'s conceited date and finds himself facing a duel with a renowned swordsman, and only one night to learn how to fence. ',
			'Episode 5 - ' . failnet::X02 . 'Safe' . failnet::X02 . ' - Mal must choose which crew members to save when one is gravely wounded and two others are kidnapped. Simon finds an uneasy haven in a remote village, but River\'s uncanny perceptions jeopardize the Tams\' temporary safety.',
			'Episode 6 - ' . failnet::X02 . 'Our Mrs. Reynolds' . failnet::X02 . ' - As an unexpected reward for an unpaid job, Mal finds himself married to a naïve, subservient young woman named Saffron. The crew are amused at his discomfort and Book lectures him on propriety, but things are not as smoothly straightforward as they thought them to be. ',
			'Episode 7 - ' . failnet::X02 . 'Jaynestown' . failnet::X02 . ' - Returning to a planet where he ran into some serious trouble years ago, Jayne discovers that he\'s become a local folk legend. Mal decides to use this entertaining distraction to complete a job, but some unfinished business may derail his plans. ',
			'Episode 8 - ' . failnet::X02 . 'Out of Gas' . failnet::X02 . ' - After Serenity suffers a catastrophe that leaves her crew with only hours of oxygen, flashbacks show how Mal and Zoe acquired Serenity and assembled their motley band. ',
			'Episode 9 - ' . failnet::X02 . 'Ariel' . failnet::X02 . ' - Hard up for cash, Serenity takes on a job from Simon: help him get a thorough diagnostic of River in return for the opportunity to loot the vast medical stores of an Alliance hospital on central world Ariel. But River\'s pursuers are hot on their trail, and they receive some unexpected inside help. ',
			'Episode 10 - ' . failnet::X02 . 'War Stories' . failnet::X02 . ' - Angered at Zoe\'s unshakable war connection to Mal, Wash demands a shot at a field assignment. Unfortunately, crime lord Niska chooses this moment to exact a brutal vengeance for Mal\'s failure to complete an earlier job. ',
			'Episode 11 - ' . failnet::X02 . 'Trash' . failnet::X02 . ' - Saffron returns to plague Serenity with a scheme to steal a rare antique weapon from a wealthy landowner. Unfortunately for Mal, she neglects to mention just how she came across the information needed to break into the landowner\'s home. ',
			'Episode 12 - ' . failnet::X02 . 'The Message' . failnet::X02 . ' - A former Independence soldier who had served with Mal and Zoe returns in a dramatic manner, with a vicious Alliance officer chasing after him for some unusual smuggled goods. ',
			'Episode 13 - ' . failnet::X02 . 'Heart of Gold' . failnet::X02 . ' - A Companion-trained friend of Inara\'s who runs a brothel calls for help from Serenity when a local bigwig reveals his intentions to take “his” baby from the girl he impregnated. ',
			'Episode 14 - ' . failnet::X02 . 'Objects in Space' . failnet::X02 . ' - Serenity encounters a ruthlessly professional bounty hunter, Jubal Early, who will stop at nothing to retrieve River. But River, feeling unwelcome on the ship, takes a novel approach to escaping from the long arm of the Alliance. ',
		);
	}
	if(isset($episodes[$number - 1]))
	{
		return $episodes[$number - 1];
	}
	else
	{
		return 'No such Firefly episode found.';
	}
}
?>