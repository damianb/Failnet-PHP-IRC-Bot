<?php
$modules[] = 'getfrompastebin';
$help['getfrompastebin']='No auth required.
This modules is for submitting code for Kai_ to review and possibly include into '.$nick.'.
'.$x02.'|get <place>'.$x02.' - downloads code from http://pastebin.com/<place> and puts it in a file to be reviewed.';

function getfrompastebin($string) {
	$pastebin = file_get_html('http://pastebin.com/'.$string);
	$content = $pastebin->find('div[class=de1]', 0)->plaintext;
	if ((empty($content))||($content='&nbsp;')) {
		return array_rand(array_flip(array('SOMEBODY SET US UP THE BOMB', 'DOES NOT COMPUTE', 'Nope.', 'Sorry, YOU FAIL', 'V rapbhagrerq snvy!')));
	} else { // I know, I don't need the else. But I want to use it for the indentation!
		if (file_put_contents('data/to_review/'.$string, $content)) {
			return array_rand(array_flip(array('Got it.', 'I have it.', 'Yos', 'No bombs here.', 'Done.')));
		} else {
			return array_rand(array_flip(array('Oh noes! I haz found fail.', 'The oboes stopped me, sorry.', $nick.', what does the scouter say about his FAIL level? IT\'S OVER 9000!')));
		}
	}
}
?>