# SVG title plugin

Contributors: vladimir gavrilovskih  
Tags: svg, header, title, animation  
Tested up to: 5.7  
Stable tag: 1.0  
License: GPLv2 or later  

Allows to transform text title to SVG image using Google fonts collection, adding different colors and animation of appearance.

## Description

SVG title plugin allows to create animated SVG images from text using Google fonts. After SVG title created, it can be used in a post or a text widget by adding a shortcode.

SVG title options:

 * Title: is the title you see in SVG.
 * Font Subset: is a font subset selector. Allows easier to select exact font by removing all other fonts in font selector without such subset.
 * Font: is a font that will be used to render title into SVG. All fonts taken from Google Font.
 * Variant: is one of possible variant of the selected font - italic, regular, bold, etc.
 * Size: is letters size of the title.
 * Stroke: width is stroke width in pixels.
 * Animation speed: in case you wish to add animation to the title, you can add four parameters:
	pause before outline drawing animation will start;
	duration of the outline drawing animation;
	pause before color filling animation will start;
	duration of the color filling animation;

Animation starts if SVG title is visible.

 * Outline color: is a color that used to paint the outline of the letters.
 * Text color: is a color that fills letters inside.

## Installation

Upload the Loginizer plugin to your blog, Activate it.

## ChangeLog

### Version 1.0.0

* Initial release.
