# iThemes ZXCVBN PHP Port

The port was done based on the CoffeeScript version by Dropbox: https://github.com/dropbox/zxcvbn

The main class is `ITSEC_Zxcvbn`. It is meant to be instantiated and has one method called `test_password()`. The `test_password` method takes a password and an optional array of strings to penalize. The array of strings should hold things like username, first name, last name, etc, anything that would make a password less secure. It is treated like a dictionary (a small one generally, so a particularly powerful one).

`ITSEC_Zxcvbn_Matcher` is used as the “matcher” which basically means it looks for various things that can be recognized in the password. It uses classes that extend `ITSEC_Zxcvbn_Match`, each of which will contain a match() static method used to actually look for it’s specific kinds of matches. The various matchers are:

* ITSEC_Zxcvbn_Dictionary_Match
* ITSEC_Zxcvbn_Dictionary_Reverse_Match
* ITSEC_Zxcvbn_Dictionary_L33t_Match
* ITSEC_Zxcvbn_Spatial_Match
* ITSEC_Zxcvbn_Repeat_Match
* ITSEC_Zxcvbn_Sequence_Match
* ITSEC_Zxcvbn_Regex_Match
* ITSEC_Zxcvbn_Date_Match
* ITSEC_Zxcvbn_Bruteforce_Match

It creates an array of all possible matches.

Those matches are passed to the scorer `ITSEC_Zxcvbn_Scorer`. The `most_guessable_match_sequence()` method is used to find the best set of matches to cover the whole password in the fewest number of guesses. Each matcher has an `estimate_guesses()` method, which is used as a way to score the sub match and help choose the least expensive combination of matches.

An instance of `ITSEC_Zxcvbn_Results` is returned. The most important thing there is the score, which is a number 0-4 with 0 being incredibly weak and 4 being strong. It also includes the estimated number of guesses required to crack the password, the optimat sequence of matches, a set of estimated times to crack the password, and some "user friendly" feedback for why the password was graded down (if it was).

## Matchers
#### ITSEC_Zxcvbn_Dictionary_Match
This one matches words from various dictionaries. The penalty strings passed into  `ITSEC_Zxcvbn→test_password()` are one dictionary. There are also dictionaries loaded from `matchers/ranked_frequency_lists.json` which include:
* passwords: Common Passwords
* english_wikipedia: Common words harvested from Wikipedia
* female_names: Common female first names from the US census
* male_names: Common male first names from the US census
* surnames: Common last names from the US census
* us_tv_and_film: Common words harvested from US TV and Film

#### ITSEC_Zxcvbn_Dictionary_Reverse_Match
This is just like the dictionary matcher but reverses the words, finding things like “aidem” instead of “media”.

#### ITSEC_Zxcvbn_Dictionary_L33t_Match
This uses the same dictionaries, but first does L33t replacements, so “m3d|@” becomes “media” and is matched.

#### ITSEC_Zxcvbn_Spatial_Match
Spacial matching loads adjacency graphs for Qwerty, Dvorak, keypad, and Mac keypad. It finds keyboard patterns, even ones with turns (more turns is stronger). For Qwerty it will find things like “asdertgb”.

#### ITSEC_Zxcvbn_Repeat_Match
Repeat matching looks for the same character being repeated, such as “aaa”.

#### ITSEC_Zxcvbn_Sequence_Match
Sequences are based on character codes, so they work for numbers like “12345” and letters like “rstuv”. It matches backwards too, such as “54321” and “vutsr”

#### ITSEC_Zxcvbn_Regex_Match
The Regex matcher really just looks for four digit years between 1900 and 2019.

#### ITSEC_Zxcvbn_Date_Match
The date matcher matches dates in many formats, with both two and four digit years, and with and without separators.

#### ITSEC_Zxcvbn_Bruteforce_Match
Bruteforce is basically the fall back matcher. It can match anything but is costly.

## Building JSON Data Files
This code can be used to generate the `ranked_frequency_lists.json` and `adjacency_graphs.json` files, both of which should go in the matchers directory.

```
git clone https://github.com/dropbox/zxcvbn.git \
&& coffee -cb zxcvbn/src/frequency_lists.coffee \
&& node -e "var frequency_lists = require('./zxcvbn/src/frequency_lists.js'); console.log( JSON.stringify( frequency_lists ) );" > frequency_lists.json \
&& php -r '$f=json_decode(file_get_contents("frequency_lists.json"));$r=array();foreach($f as $d=>$l){$r[$d]=array();foreach($l as $k=>$w){$r[$d][$w]=$k+1;}}echo json_encode($r);' > ranked_frequency_lists.json \
&& coffee -cb zxcvbn/src/adjacency_graphs.coffee \
&& node -e "var adjacency_graphs = require('./zxcvbn/src/adjacency_graphs.js'); console.log( JSON.stringify( adjacency_graphs ) );" > adjacency_graphs.json \
&& rm -rf zxcvbn
```
