<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet  [
	<!ENTITY nbsp   "&#160;">
	<!ENTITY copy   "&#169;">
	<!ENTITY reg    "&#174;">
	<!ENTITY trade  "&#8482;">
	<!ENTITY mdash  "&#8212;">
	<!ENTITY ldquo  "&#8220;">
	<!ENTITY rdquo  "&#8221;"> 
	<!ENTITY pound  "&#163;">
	<!ENTITY yen    "&#165;">
	<!ENTITY euro   "&#8364;">
]>
<xsl:stylesheet version="1.0"
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="xml" encoding="utf-8" omit-xml-declaration="yes" />
	<xsl:template name="Header">
		<div id="header">
			<div class="center_wrapper">
				<div id="toplinks">
					<div id="toplinks_inner">
						Welcome, <xsl:value-of select="/page/kernel/session/username" /> | <a href="#">Help</a>
					</div>
				</div>

				<div id="site_title">
					<h1 class="left"><span>Audio/Video</span>Management</h1>
				</div>
				<div class="clearer"><xsl:comment></xsl:comment></div>
			</div>
			<div id="navigation">
				<div class="center_wrapper">
					<ul>
						<xsl:apply-templates select="/page/kernel/menu/item"/>
					</ul>
					<div class="clearer"><xsl:comment></xsl:comment></div>
				</div>
			</div>
		</div>
	</xsl:template>
</xsl:stylesheet>
