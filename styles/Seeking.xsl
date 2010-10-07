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

						function pageClick(pageclickednumber) {
							$("#pager").pager({ pagenumber: pageclickednumber, pagecount: 15, buttonClickCallback: pageClick });
				            //$("#result").html("Clicked Page " + pageclickednumber);
				        };
						
						$("#pager").pager({ pagenumber: 1, pagecount: 15, buttonClickCallback: pageClick});
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
								<div class="box_title">Insert into database</div>
								<div class="box_content" style="display: none;">
									<form method="post">
										<xsl:attribute name="action"> <xsl:value-of
												select="/page/addurl" /> </xsl:attribute>
												
										<label>Name<input type="text" name="name" maxlength="255" size="32"></input></label>
										<label>Year<input type="text" name="year" class="editNumeric" maxlength="4" size="4"></input></label>
										<label>Url<input type="text" name="url" maxlength="255" size="32"/></label>
										<input type="submit" name="submit" value="Save" />
									</form>
								</div>
							</div>
							<h1>Currently seeking</h1>
							<div id="pager" ></div>
							<div class="clearer"><xsl:comment></xsl:comment></div>
							<xsl:call-template name="editableSeekList"/>
						</div>
					</div>
				</div>
				<xsl:call-template name="Footer" />

			</body>
		</html>
	</xsl:template>
</xsl:stylesheet>
