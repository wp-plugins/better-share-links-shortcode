=== Better Share Links shortcode ===
Contributors: peeping4dsun
Tags: screenshot, embed, links, URLs, shortcode
Requires at least: 3.7
Tested up to: 4.1
Stable tag: 1.0

A complete template for sharing links using a generated screenshot
== Description ==

<strong>Better Share Links shortcode plugin </strong> -
<ul>
<li>The better way of sharing important links is to share them with their screenshot.</li>
<li>The plugin makes sharing links(URLs) more user friendly.</li>
<li>The plugin require API key from page2images.com(Read step 7 of the installation instructions )</li>
<li>The free account gives the user 3000 calls per month.</li>
<li>The free account works only on http URL addresses.</li> 
<li>The author of this plugin is not associated in any way with the API provider.</li>
<li>The shortcode will output a standard clickable link, until a screenshot is generated.</li>
<li>Text button "Share link" within TinyMCE editor that outputs [better_share_link url="|||||" ]</li>
<li>More options coming to this plugin, as an admin page, in which the webmaster can include a custom CSS, enabling and disabling features, etc.</li>
</ul>
<strong>The current template of the shortcode is</strong> -
<ul>
<li>A title within H3 tag(the title is obtained via "PHP Simple HTML DOM Parser").</li>
<li>A subtitle within H6 tag containing the URL address itself</li>
<li>The image of the screenshot</li>
<li>All of the three parts are links.</li>
</ul>
<strong>The SEO optimized output of the shortcode contains</strong> -
<li>Alt tag, title tag of the image are equal to the title of the shared URL.</li>
<li>The filename of the image is the sanitized title of the URL, i.e. the whitespaces are replaced with dashes(-).</li>
<li>The height and the width of image are set within the 'img' tag.</li>
<li>All the outputted links are nofollow.</li>

== Installation ==

1. Upload `better_share_links.zip` onto your local computer.
2. Go to your WordPress Dashboard and select <strong>Plugins >> Add New</strong>.
3. Click on the <strong>Upload</strong> option at the top and select the `better_share_links.zip file` you just downloaded.
4. Click on <strong>Install</strong>.
5. Activate the plugin through the 'Plugins' menu in WordPress
6. Click on the text button 'Share link' inside the visual text editor(when editing or publishing page or post)
7. Sign up for the free API of page2images.com and insert it on line 78 in the php file of this plugin 
8. The button will output [better_share_link url="||||||"]
9. Paste URL between any of the bars ||||||

== Frequently Asked Questions ==



== Screenshots ==
1. Wireframe of the plugin's template
2. Screenshot of the output

== Changelog ==

