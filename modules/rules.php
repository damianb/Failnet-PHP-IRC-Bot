<?php
$failnet->modules[] = 'rules';
$loaded['rules'] = true;

$help['rules'] = 'Enter in ' . failnet::X02 . '|rule (rule number)' . failnet::X02 . ' to look up a specified rule of the internet.';

function rule($number)
{
	static $rules;
	if(empty($rules) || !is_array($rules))
	{
		$rules = array('Do not talk about /b/',
			'Do NOT talk about /b/',
			'We are Anonymous.',
			'Anonymous is legion.',
			'Anonymous does not forgive, Anonymous does not forget.',
			'Anonymous can be horrible, senseless, uncaring monster.',
			'Anonymous is still able to deliver.',
			'There are no real rules about posting.',
			'There are no real rules about moderation either - enjoy your ban.',
			'If you enjoy any rival sites - DON\'T.',
			'You must have pictures to prove your statement.',
			'Lurk moar - it\'s never enough.',
			'Nothing is Sacred.',
			'Do not argue with a troll - it means that they win.',
			'The more beautiful and pure a thing is, the more satisfying it is to corrupt it.',
			'There are NO girls on the internet.',
			'A cat is fine too.',
			'One cat leads to another.',
			'The more you hate it, the stronger it gets.',
			'It is delicious cake. You must eat it.',
			'It is delicious trap. You must hit it.',
			'/b/ sucks today.',
			'Cock goes in here.',
			'You will never have sex.',
			'????',
			'PROFIT!',
			'It needs more Desu. No exceptions.',
			'There will always be more fucked up shit than what you just saw.',
			'You can not divide by zero (just because the calculator says so).',
			'No real limits of any kind apply here - not even the sky',
			'CAPSLOCK IS CRUISE CONTROL FOR COOL.',
			'EVEN WITH CRUISE CONTROL YOU STILL HAVE TO STEER.',
			'Desu isn\'t funny. Seriously guys. It\'s worse than Chuck Norris jokes.',
			'There is porn of it. No exceptions.',
			'If no porn is found of it, it will be created.',
			'No matter what it is, it is somebody\'s fetish. No exceptions.',
			'Even one positive comment about Japanese things can make you a weeaboo.',
			'When one sees a lion, one must get into the car',
			'There is furry porn of it. No exceptions.',
			'The pool is always closed due to AIDS (and stingrays, which also have AIDS).',
			'If there isn\'t enough just ask for Moar.',
			'Everything has been cracked and pirated.',
			'DISREGARD THAT I SUCK COCKS',
			'The internet is not your personal army.',
			'Rule 45 is a lie.',
			'The cake is a lie.',
			'If you post it, they will cum.',
			'It will always need moar sauce.',
			'The internet makes you stupid.',
			'Anything can be a meme.',
			'Longcat is looooooooooong.',
			'If something goes wrong, Ebaums did it.',
			'Anonymous is a virgin by default.',
			'Moot has cat ears, even in real life. No exceptions.',
			'CP is awwwright, but DSFARGEG will get you b&.',
			'Don\'t mess with football.',
			'MrSpooky has never seen so many ingrates.',
			'Anonymous does not "buy", he downloads.',
			'The term "sage" does not refer to the spice.',
			'If you say Candlejack, you w',
			'You cannot divide by zero.',
			'The internet is SERIOUS FUCKING BUSINESS.',
			'If you do not believe it, then it must be habeebed for great justice.',
			'Not even Spider-Man knows how to shot web.',
			'Mitchell Henderson was an hero to us all.',
			'This is not lupus, it\'s SPARTAAAAAAAAAA.',
			'One does not simply shoop da whoop into Mordor.',
			'Katy is bi, so deal w/it.',
			'LOL SIXTY NINE AMIRITE?',
			'Also, cocks.',
			'This is a showdown, a throwdown, hell no I can\'t slow down, it\'s gonna go.',
			'Anonymous did NOT, under any circumstances, tk him 2da bar|?',
			'If you express astonishment at someone\'s claim, it is most likely just a clever ruse.',
			'If it hadn\'t been for Cotton Eyed Joe, Anonymous would have been married a long time ago.',
			'Around Snacks, CP is lax.',
			'All numbers are at least 100 but always OVER NINE THOUSAAAAAND.',
			'Hal Turner definitely needs to gb2/hell/.',
			'Mods are fucking fags. No exceptions.',
			'All Caturday threads will be bombarded with Zippocat. No exceptions.',
			'No matter how cute it is, it probably skullfucked your mother last night.',
			'That\'s not mud.',
			'Steve Irwin\'s death is really, really funny.',
			'The Internet is SERIOUS FUCKING BUSINESS.',
			'Rule 87 is true.',
			'Yes, it is some chickens.',
			'Bobba bobba is bobba.',
			'Rule 84 is false. OH SHI-',
			'If your statement is preceded by "HAY GUYZ", then you are not doing it right.',
			'If you cannot understand it, it is machine code.',
			'Anonymous still owes Hal Turner one trillion U.S. dollars.',
			'Spengbab Sqarpaint is luv Padtwick Zhstar iz fwend.',
			'Disregard Bigmike, he sucks cocks.',
			'Secure tripcodes are for jerks.',
			'If someone herd u liek Mudkips, deny it constantly for the lulz.',
			'Combo breakers are inevitable. If the combo is completed successfully, it is gay.',
			'I am a huge faggot. Please rape my face.',
			'Shit sucks and will never be stickied.',
			'Bricks must are required to be shat whenever Anonymous is surprised.',
			'If you have no bricks to shit, you are made of fail and AIDS.',
			'ZOMG NONE'
		);
	}
	if(isset($rules[$number - 1]))
	{
		return $rules[$number - 1];
	}
	else
	{
		return 'No such rule.';
	}
}
?>