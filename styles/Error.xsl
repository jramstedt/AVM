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
		doctype-public="-//W3C//DTD XHTML 1.1//EN" doctype-system="http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd" />
	<xsl:include href="Kernel.xsl" />
	<xsl:include href="Header.xsl" />
	<xsl:include href="Footer.xsl" />
	<xsl:template match="/page">
		<html>
			<head>
				<link id="stylesheet" rel="stylesheet" href="default.css" type="text/css"
					charset="utf-8" />
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<title>
					<xsl:value-of select="/page/title" />
				</title>
			</head>
			<body>
				<xsl:call-template name="Header" />

				<div id="content">
					<div id="main">
						<div class="center_wrapper">
							<p>
								<h1>
									<xsl:value-of select="/page/code" />
								</h1>
								<xsl:value-of select="/page/text" />
							</p>
						</div>
					</div>
				</div>
				<xsl:call-template name="Footer" />

			</body>
		</html>
	</xsl:template>
</xsl:stylesheet>
