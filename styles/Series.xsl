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
				<link id="stylesheet" rel="stylesheet" href="default.css" type="text/css"
					charset="utf-8">
				</link>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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
				<script type="text/javascript">
					$(document).ready(function() {
						var videoformat = jQuery.parseJSON('<xsl:value-of select="/page/videoformatjson" />');
						
						$.each(videoformat, function(i,item){
							<![CDATA[$('<option />').val(i).append(item).appendTo('select[name=format]');]]>
						});
						
						$("select[name=serieid]").change(function () {
							var seasonList = $(this).closest('form').find('select[name=seasonid]');
						    seasonList.empty();
							$.getJSON('<xsl:value-of select="/page/seasonlisturl" />'+'/id/'+this.value,
								function(data){
							      $.each(data, function(i,item){
							      	<![CDATA[$('<option />').val(item.id).append(item.season).appendTo(seasonList);]]>
						          });
							});
						});
						
						$(document).ajaxSuccess(function() {
						  setEditable();
						});
						
						function setEditable() {
							$('.edit').each(function(index) {
							    var id = $(this).closest(".box").attr("id");
								$(this).editable('<xsl:value-of select="/page/editurl" />'+'/id/'+id, {
						        	indicator : 'Saving...',
						        	tooltip   : 'Click to edit...',
						        	style   : 'display: inline'
					        	});
					        });
					        
					        $('.editNumeric').each(function(index) {
						        var id = $(this).closest(".box").attr("id");
						        $(this).editable('<xsl:value-of select="/page/editurl" />'+'/id/'+id, {
						        	indicator : 'Saving...',
						        	tooltip   : 'Click to edit...',
						        	style   : 'display: inline'
						        });
						    });
	
							$('.editDropdown').each(function(index) {
					        	var id = $(this).closest(".box").attr("id");
						        $(this).editable('<xsl:value-of select="/page/editurl" />'+'/id/'+id, {
									data   : '<xsl:value-of select="/page/videoformatjson" />',
						        	indicator : 'Saving...',
						        	tooltip   : 'Click to choose...',
						        	style   : 'display: inline',
						        	type   : 'select',
						        	submit : 'Select'
						        });
						    });
						    
					        $('.editCheckBox').each(function(index) {
					        	var id = $(this).closest(".box").attr("id");
						        $(this).editable('<xsl:value-of select="/page/editurl" />'+'/id/'+id, {
						        	indicator : 'Saving...',
						        	tooltip   : 'Click to toggle...',
						        	style   : 'display: inline',
						        	type   : 'checkbox',
						        	onblur: 'submit'
						        });
						    });
						};
						
						setEditable();
						
						$.getJSON('<xsl:value-of select="/page/serielisturl" />',
							function(data){
						      $.each(data, function(i,item){
						      	<![CDATA[$('<option />').val(item.id).append(item.name).appendTo($("select[name=serieid]"));]]>
					          });
						});
					});
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
								<div class="box_title">Add serie</div>
								<div class="box_content" style="display: none;">
									<form method="post">
										<xsl:attribute name="action"> <xsl:value-of
											select="/page/addserieurl" /> </xsl:attribute>
											
										<label>Name<input type="text" name="name" maxlength="255" size="42"></input></label>
										<label>Year<input type="text" name="year" class="editNumeric" maxlength="4" size="4"></input></label>
										<label>Url<input type="text" name="url" maxlength="255" size="42"/></label><br/>
										<label>Torrent<input type="text" name="torrent" maxlength="255" size="42"/></label>
										<label>File<input type="text" name="file" maxlength="255" size="42"/></label>
										<input type="submit" name="submit" value="Save" />
									</form>
								</div>
							</div>
							<div class="box">
								<div class="box_title">Add season</div>
								<div class="box_content" style="display: none;">
									<form method="post">
										<xsl:attribute name="action"> <xsl:value-of
											select="/page/addseasonurl" /> </xsl:attribute>
											
										<select name="serieid">
											<xsl:comment></xsl:comment>
										</select>
										<label>Season<input type="text" name="season" class="editNumeric" maxlength="2" size="2"></input></label>
										<label>State<input type="text" name="state" maxlength="255"/></label><br/>
										<label>Torrent<input type="text" name="torrent" maxlength="255" size="42"/></label>
										<label>File<input type="text" name="file" maxlength="255" size="42"/></label>
										<label>Watched<input type="checkbox" name="watched"></input></label>
										<input type="submit" name="submit" value="Save" />
									</form>
								</div>
							</div>
							<div class="box">
								<div class="box_title">Add episode</div>
								<div class="box_content" style="display: none;">
									<form method="post">
										<xsl:attribute name="action"> <xsl:value-of
												select="/page/addepisodeurl" /> </xsl:attribute>
												
										<select name="serieid">
											<xsl:comment></xsl:comment>
										</select>
										<label>Season
											<select name="seasonid">
												<xsl:comment></xsl:comment>
											</select>
										</label>
										<label>Episode<input type="text" name="episode" class="editNumeric" maxlength="2" size="2"></input></label>
										<label>Title<input type="text" name="title" maxlength="255" size="32"></input></label>
										<label>Format<select name="format"><xsl:comment></xsl:comment></select></label><br/>
										<label>Torrent<input type="text" name="torrent" maxlength="255" size="42"/></label>
										<label>File<input type="text" name="file" maxlength="255" size="42"/></label>
										<label>Watched<input type="checkbox" name="watched"></input></label>
										<input type="submit" name="submit" value="Save" />
									</form>
								</div>
							</div>
							<h1>Series</h1>
							<xsl:call-template name="editableSerieList"/>
						</div>
					</div>
				</div>
				<xsl:call-template name="Footer" />

			</body>
		</html>
	</xsl:template>
</xsl:stylesheet>
