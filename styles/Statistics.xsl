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
	<xsl:include href="AVM.xsl" />
	<xsl:include href="Header.xsl" />
	<xsl:include href="Footer.xsl" />
	<xsl:template match="/page">
		<html>
			<head>
				<link id="stylesheet" rel="stylesheet" href="default.css" type="text/css" charset="utf-8">
				</link>
				<title>
					<xsl:value-of select="/page/title" />
				</title>
				<script src="js/jquery-1.4.2.js" type="text/javascript">
					<xsl:comment></xsl:comment>
				</script>
				<script src="js/jquery.jeditable.js" type="text/javascript">
					<xsl:comment></xsl:comment>
				</script>
				<script src="js/jquery.jeditable.checkbox.js" type="text/javascript">
					<xsl:comment></xsl:comment>
				</script>
				<script src="js/jquery-ui-1.8.1.custom.min.js" type="text/javascript">
					<xsl:comment></xsl:comment>
				</script>
				<script src="js/jquery.jqplot.min.js" type="text/javascript">
					<xsl:comment></xsl:comment>
				</script>
				<script src="js/jqplot.dateAxisRenderer.min.js" type="text/javascript">
					<xsl:comment></xsl:comment>
				</script>
				<script src="js/jqplot.canvasTextRenderer.min.js" type="text/javascript">
					<xsl:comment></xsl:comment>
				</script>
				<script src="js/jqplot.canvasAxisLabelRenderer.min.js" type="text/javascript">
					<xsl:comment></xsl:comment>
				</script>
				<script src="js/jqplot.trendline.min.js" type="text/javascript">
					<xsl:comment></xsl:comment>
				</script>
				<script src="js/jqplot.cursor.min.js" type="text/javascript">
					<xsl:comment></xsl:comment>
				</script>
				<script src="js/jquery.pager.js" type="text/javascript">
					<xsl:comment></xsl:comment>
				</script>
				<script src="js/avm.js" type="text/javascript">
					<xsl:comment></xsl:comment>
				</script>
				<![CDATA[
				<!--[if IE]>
					<script language="javascript" type="text/javascript" src="js/excanvas.js">
						<xsl:comment></xsl:comment>
					</script>
				<![endif]-->
				]]>
				<script type="text/javascript">
					$.jqplot.config.enablePlugins = true;
					
					$(document).ready(function() {
					
					});

					$.getJSON('<xsl:value-of select="/page/getfilesystemusageurl" />', setFilesystemUsagePlot);
					
					function setFilesystemUsagePlot(fsData) {
						$.jqplot('filesystemUsage', fsData.usage, {
							title:'Filesystem usage',
							axes:{
								xaxis:{ 
									renderer:$.jqplot.DateAxisRenderer,
									tickOptions:{formatString:'%d.%m.%Y %R'},
	       							autoscale: true
								},
								yaxis:{
									label: fsData.ylabel,
									tickOptions:{ formatString:'%.3f'},
	       							autoscale: true,
	       							max: fsData.ymax,
	       							min: fsData.ymin
								}
							},
							legend: {
						        show: true,
						        location: 'sw',
						    },
						    cursor:{
						    	zoom:true,
						    	showTooltip:false
						    },
						    seriesDefaults: {
						    	 markerOptions: { show:false }
						    },
							series:fsData.series
						});
					}
				</script>
			</head>
			<body>
				<xsl:call-template name="Header" />

				<div id="content">
					<div id="main">
						<div class="center_wrapper">
							<div id="infobar">
								<xsl:comment></xsl:comment>
								<xsl:apply-templates select="/page/infobox/item"/>
							</div>
							<h1>Statistics</h1>
							<div id="filesystemUsage" style="height:400px;width:800px; ">
								<xsl:comment></xsl:comment>
							</div>
						</div>
					</div>
				</div>
				<xsl:call-template name="Footer" />

			</body>
		</html>
	</xsl:template>
</xsl:stylesheet>
