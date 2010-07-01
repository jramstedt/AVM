<?xml version="1.0" encoding="utf-8"?>
	<!--
		DWXMLSource="http://acart.dy.fi/AVM/Mainpage/xmlonly/1"
	-->
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
				<link id="stylesheet" rel="stylesheet" href="default.css" type="text/css"
					charset="utf-8">
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
				<script src="js/jquery.pager.js" type="text/javascript">
					<xsl:comment></xsl:comment>
				</script>
				<script src="js/avm.js" type="text/javascript">
					<xsl:comment></xsl:comment>
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

							<div class="box">
								<p>
									Torrents:
									<xsl:value-of select="/page/statistics/torrents" />
									Movies:
									<xsl:value-of select="/page/statistics/movies" />
									Series:
									<xsl:value-of select="/page/statistics/series" />
									Uncategorized:
									<xsl:value-of select="/page/statistics/uncategorized" />
								</p>
							</div>
							<div class="box">
								<div class="box_title">Videos not seen yet</div>
								<div class="box_content">
									<xsl:comment></xsl:comment>
									<xsl:apply-templates select="/page/list/movie" mode="list">
										<xsl:sort select="name"/>
									</xsl:apply-templates>
									<xsl:apply-templates select="/page/list/episode" mode="list">
										<xsl:sort select="file"/>
									</xsl:apply-templates>
								</div>
							</div>
							<div class="box">
								<div class="box_title">
									<div class="left">
										Torrents from feeds
									</div>
									<div class="right">
										<form method="post">
											<xsl:attribute name="action"> <xsl:value-of
												select="/page/filterurl" /> </xsl:attribute>
												
											<label>Filter<input name="filter" type="text" class="nospacing" maxlength="24" size="24"><xsl:attribute name="value"><xsl:value-of select="/page/settings/filter" /></xsl:attribute></input></label>
											<label>From<input name="timelimit" type="text" class="datepicker nospacing" maxlength="10" size="10"><xsl:attribute name="value"><xsl:value-of select="/page/settings/timelimit" /></xsl:attribute></input></label>
										</form>
									</div>
									<div style="clear:both">
										<xsl:comment></xsl:comment>
									</div>
								</div>
								<div class="box_content">
									<xsl:comment></xsl:comment>
									<xsl:apply-templates select="/page/list/feeditem">
										<xsl:sort select="publishedDate" data-type="number" order="descending"/>
									</xsl:apply-templates>
								</div>
							</div>
							<xsl:comment>
							<div class="box">
								<div class="box_title">New Torrents</div>
								<div class="box_content">
									<table width="100%" border="0" cellspacing="0"
										cellpadding="0" summary="Filtered torrents" class="data_table">
										<tr>
											<th scope="col">Torrent</th>
											<th scope="col" width="100">Download!</th>
										</tr>
										<xsl:for-each select="/page/filtered/torrent">
											<xsl:sort select="*[name() = /page/newtorrent/sort]"
												data-type="{/page/newtorrent/sort/@type}" order="{/page/newtorrent/sort/@order}" />
											<tr>
												<td>
													<xsl:value-of select="name" />
												</td>
												<td>
													<a>
														<xsl:attribute name="href"> <xsl:value-of
															select="downloadurl" /> </xsl:attribute>
														watched
													</a>
												</td>
											</tr>
										</xsl:for-each>
									</table>
								</div>
							</div>
							<div class="box">
								<div class="box_title">New Torrents</div>
								<div class="box_content">
									<table width="100%" border="0" cellspacing="0"
										cellpadding="0" summary="Waiting for completion" class="data_table">
										<tr>
											<th scope="col">Torrent</th>
											<th scope="col" width="100">Percentage</th>
										</tr>
										<xsl:for-each select="/page/downloading/torrent">
											<xsl:sort select="*[name() = /page/downloading/sort]"
												data-type="{/page/downloading/sort/@type}" order="{/page/downloading/sort/@order}" />
											<tr>
												<td>
													<xsl:value-of select="name" />
												</td>
												<td>
													<xsl:value-of select="percentage" />
												</td>
											</tr>
										</xsl:for-each>
									</table>
								</div>
							</div>
							</xsl:comment>
						</div>
					</div>
				</div>
				<xsl:call-template name="Footer" />

			</body>
		</html>
	</xsl:template>
</xsl:stylesheet>
