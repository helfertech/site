/**
 *     Twitter real time search scrolling
 *     Copyright (C) 2011 - 2013 www.gopiplus.com
 * 
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 * 
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 * 
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */	

function scrollTwitterrealSlider() {
	objTwitterrealSlider.scrollTop = objTwitterrealSlider.scrollTop + 1;
	twitterreal_scrollPos++;
	if ((twitterreal_scrollPos%twitterreal_heightOfElm) == 0) {
		twitterreal_numScrolls--;
		if (twitterreal_numScrolls == 0) {
			objTwitterrealSlider.scrollTop = '0';
			TwitterrealSliderContent();
		} else {
			if (twitterreal_scrollOn == 'true') {
				TwitterrealSliderContent();
			}
		}
	} else {
		setTimeout("scrollTwitterrealSlider();", 10);
	}
}

var IRNum = 0;
/*
Creates amount to show + 1 for the scrolling ability to work
scrollTop is set to top position after each creation
Otherwise the scrolling cannot happen
*/
function TwitterrealSliderContent() {
	var tmp_IR = '';

	w_IR = IRNum - parseInt(twitterreal_numberOfElm);
	if (w_IR < 0) {
		w_IR = 0;
	} else {
		w_IR = w_IR%TwitterrealSlider.length;
	}
	
	// Show amount of IR
	var elementsTmp_IR = parseInt(twitterreal_numberOfElm) + 1;
	for (i_IR = 0; i_IR < elementsTmp_IR; i_IR++) {
		
		tmp_IR += TwitterrealSlider[w_IR%TwitterrealSlider.length];
		w_IR++;
	}

	objTwitterrealSlider.innerHTML 	= tmp_IR;	
	IRNum 				= w_IR;
	twitterreal_numScrolls 	= TwitterrealSlider.length;
	objTwitterrealSlider.scrollTop 	= '0';
	// start scrolling
	setTimeout("scrollTwitterrealSlider();", 2000);
}

