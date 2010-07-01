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
						
						$("select.type").change(function () {
							var value = this.value;
							
							$(this).nextAll('div').not('.box').each(function (i) {
						        if ($(this).is('.'+value)) {
						          $(this).css('display', 'inline-block');
						        } else {
						          $(this).css('display', 'none');
						        }
						      });
						    
						    if(value == 'serie')
						    	$(this).nextAll('.box').find('select.type').val('season').change();
						    else if(value == 'season')
						    	$(this).nextAll('.box').find('select.type').val('episode').change();
						   	else
						   		$(this).nextAll('.box').find('select.type').val('default').change();
						});
						
						$("select[name=serieid]").change(function () {
							var path = $(this).closest('.box');
							var torrent = $(this).parents('.box').last();
						    var seasonList = $(this).closest('form').find('select[name=seasonid]');
						    seasonList.empty();
							$.getJSON('<xsl:value-of select="/page/seasonlisturl" />/id/'+this.value+'/current/'+path.attr('id')+'/root/'+torrent.attr('id'),
								function(data){
								 	$.each(data, function(i,item){
								      	<![CDATA[var option = $('<option />').val(item.id).append(item.season).appendTo(seasonList);]]>
								      	
								      	if(item.selected)
								      		option.attr('selected', 'true');
							    	});
							});
							
							$(this).closest('div').nextAll('.box').find('select[name=serieid]').val(this.value).change();
						});
						
						$.getJSON('<xsl:value-of select="/page/serielisturl" />',
							function(data){
						      $.each(data, function(i,item){
						      	<![CDATA[$('<option />').val(item.id).append(item.name).appendTo($("select[name=serieid]"));]]>
					          });
						});
						
						$("img[src='arrow-down.gif']").click(function () {
							var jObj = $(this).prev();
							if(jObj.is('label'))
								jObj = jObj.children();
							
							var element = jObj.get(0);
							var siblings = $(this).closest('.box').nextAll('.box').find(element.tagName+'[name='+element.name+']');
							
							var count = 1;
							siblings.each(function (i) {
							    if(this.className == 'editNumeric')
									$(this).val(parseInt(element.value) + count++);
								else
									$(this).val(element.value);
									
								$(this).change();
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
							<h1>Unhandled</h1>
							<xsl:apply-templates select="/page/list"/>
						</div>
					</div>
				</div>
				
				<xsl:call-template name="Footer" />

			</body>
		</html>
	</xsl:template>
</xsl:stylesheet>
