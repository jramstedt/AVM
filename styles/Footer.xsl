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
	<xsl:output method="xml" encoding="utf-8"
		omit-xml-declaration="yes" />
	<xsl:template name="Footer">
		<!-- <div id="footer">&copy; 2010</div>  -->

		<div id="footer">
			<div class="center_wrapper">
				<xsl:apply-templates select="/page/kernel/kernelDebug" />
			</div>
		</div>
	</xsl:template>
</xsl:stylesheet>
