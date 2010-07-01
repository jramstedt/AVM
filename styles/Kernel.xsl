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
	
	<xsl:template match="kernel/infobox/item">
		<div>
			<xsl:attribute name="class"> <xsl:value-of select="type" /> </xsl:attribute>
			<xsl:value-of select="text" />
		</div>
	</xsl:template>
	
	<xsl:template match="kernel/menu/item">
		<li>
			<xsl:if test="current">	
				<xsl:attribute name="class">current_page_item</xsl:attribute>
			</xsl:if>
			<a>
				<xsl:attribute name="href"> <xsl:value-of
					select="href" /> </xsl:attribute>

				<xsl:value-of select="text" />
			</a>
		</li>
	</xsl:template>
	
	<xsl:template match="kernel/kernelDebug">
		<div id="debug">
			<pre>
				<xsl:value-of select="." />
			</pre>
		</div>
	</xsl:template>
</xsl:stylesheet>
