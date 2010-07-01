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
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="xml" encoding="utf-8" omit-xml-declaration="yes" />
	
	<xsl:include href="AVM.xsl" />
	
	<xsl:template match="/page">
		<ajax>
			<xsl:apply-templates select="/page/kernel/infobox/item"/>
			<xsl:apply-templates select="/page/infobox/item"/>
			<xsl:apply-templates select="/page/kernel/kernelDebug" />

			<xsl:apply-templates select="/page/remove/movie" mode="remove"/>
			<xsl:apply-templates select="/page/remove/episode" mode="remove"/>
			<xsl:apply-templates select="/page/remove/feeditem" mode="remove"/>
		</ajax>
	</xsl:template>	
</xsl:stylesheet>