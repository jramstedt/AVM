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
	
	<xsl:include href="Kernel.xsl" />
	
	<xsl:template match="movie" mode="remove">
		<remove>movie:<xsl:value-of select="id" /></remove>
	</xsl:template>
	
	<xsl:template match="movie" mode="list" xmlns="http://www.w3.org/1999/xhtml">
		<div>
			<xsl:attribute name="id">movie:<xsl:value-of select="id" /></xsl:attribute>
			<div class="left">
				<xsl:value-of select="name" />
			</div>
			<div class="right">
				<a name="watched">
					<xsl:attribute name="href"><xsl:value-of select="/page/watchedmovieurl" />/id/<xsl:value-of select="id" /></xsl:attribute>
					
					<xsl:choose>
					  <xsl:when test="watched = 1">
						<img src="exclaim.gif" alt="Unwatch this." title="Unwatch this."/>
					  </xsl:when>
					  <xsl:otherwise>
						<img src="checkmark.gif" alt="Watched this." title="Set as watched."/>
					  </xsl:otherwise>
					</xsl:choose>
				</a>
			</div>
			<div style="clear:both">
				<xsl:comment></xsl:comment>
			</div>
		</div>
	</xsl:template>
	
	<xsl:template match="movie" mode="editable" xmlns="http://www.w3.org/1999/xhtml">
		<div class="box">
			<xsl:attribute name="id">movie:<xsl:value-of select="id" /></xsl:attribute>
			<div class="box_title">
				<div class="left">
					<span class="edit">
						<xsl:attribute name="id">name:<xsl:value-of
							select="id" /></xsl:attribute>
						<xsl:value-of select="name" />
					</span>
					(
					<span class="editNumeric">
						<xsl:attribute name="id">year:<xsl:value-of
							select="id" /></xsl:attribute>
						<xsl:value-of select="year" />
					</span>
					) [
					<span class="editDropdown">
						<xsl:attribute name="id">format:<xsl:value-of
							select="id" /></xsl:attribute>
						<xsl:value-of select="format" />
					</span>
					]
				</div>
				<div class="right">
					<a name="watched">
						<xsl:attribute name="href"><xsl:value-of select="/page/watchedurl" />/id/<xsl:value-of select="id" /></xsl:attribute>
						
						<xsl:choose>
						  <xsl:when test="watched = 1">
							<img src="exclaim.gif" alt="Unwatch this." title="Unwatch this."/>
						  </xsl:when>
						  <xsl:otherwise>
							<img src="checkmark.gif" alt="Watched this." title="Set as watched."/>
						  </xsl:otherwise>
						</xsl:choose>
					</a>
				</div>
				<div class="clearer">
					<xsl:comment></xsl:comment>
				</div>
			</div>
			<div class="box_content" style="display: none;">
				<p>
					<label>Url:</label>
					<span class="edit">
						<xsl:attribute name="id">url:<xsl:value-of
							select="id" /></xsl:attribute>
						<xsl:value-of select="url" />
					</span>
				</p>
				<xsl:comment><p>
					<label>Watched:</label>
					<span class="editCheckBox">
						<xsl:attribute name="id">watched:<xsl:value-of
							select="id" /></xsl:attribute>
						<xsl:value-of select="watched" />
					</span>
				</p></xsl:comment>
				<p>
					<label>File:</label>
					<span class="edit">
						<xsl:attribute name="id">file:<xsl:value-of
							select="id" /></xsl:attribute>
						<xsl:value-of select="file" />
					</span>
				</p>
				<p>
					<label>Torrent:</label>
					<span class="edit">
						<xsl:attribute name="id">torrent:<xsl:value-of
							select="id" /></xsl:attribute>
						<xsl:value-of select="torrent" />
					</span>
				</p>
			</div>
		</div>
	</xsl:template>
	
	<xsl:template name="editableMovieList" xmlns="http://www.w3.org/1999/xhtml">
		<xsl:if test="/page/list"> 
			<div id="editableMovieList">
				<xsl:apply-templates select="/page/list/movie" mode="editable" />
			</div>
		</xsl:if>
	</xsl:template>
	
	<xsl:template match="serie" xmlns="http://www.w3.org/1999/xhtml">
		<div class="box">
			<xsl:attribute name="id">serie:<xsl:value-of select="id" /></xsl:attribute>
			<div class="box_title">
				<div class="left">
					<span class="edit">
						<xsl:attribute name="id"><xsl:value-of select="id" />:name</xsl:attribute>
						<xsl:value-of select="name" />
					</span>
					(
					<span class="editNumeric">
						<xsl:attribute name="id"><xsl:value-of select="id" />:year</xsl:attribute>
						<xsl:value-of select="year" />
					</span>
					)
				</div>
				<div class="right">
					<xsl:comment></xsl:comment>
					<xsl:if test="torrent[.!='']">
						<a name="seed">
							<xsl:attribute name="href"><xsl:value-of select="/page/seedurl" />/serie/<xsl:value-of select="id" /></xsl:attribute>
							<img src="back-forth.gif" alt="Seed torrent." title="Seed torrent."/>
						</a>
					</xsl:if>
				</div>
				<div style="clear:both">
					<xsl:comment></xsl:comment>
				</div>
			</div>
			<div class="box_content" style="display: none;">
				<p>
					<label>Url:</label>
					<span class="edit">
						<xsl:attribute name="id"><xsl:value-of
							select="id" />:url</xsl:attribute>
						<xsl:value-of select="url" />
					</span>
				</p>
				<p>
					<label>File:</label>
					<span class="edit">
						<xsl:attribute name="id"><xsl:value-of
							select="id" />:file</xsl:attribute>
						<xsl:value-of select="file" />
					</span>
				</p>
				<p>
					<label>Torrent:</label>
					<span class="edit">
						<xsl:attribute name="id"><xsl:value-of
							select="id" />:torrent</xsl:attribute>
						<xsl:value-of select="torrent" />
					</span>
				</p>
				<xsl:apply-templates select="season">
					<xsl:sort select="season" data-type="number"/>
				</xsl:apply-templates>
			</div>
		</div>
	</xsl:template>
	
	<xsl:template name="editableSerieList" xmlns="http://www.w3.org/1999/xhtml">
		<xsl:if test="/page/list"> 
			<div id="editableSerieList">
				<xsl:apply-templates select="/page/list/serie"/>
			</div>
		</xsl:if>
	</xsl:template>
	
	<xsl:template match="season" xmlns="http://www.w3.org/1999/xhtml">
		<div class="box">
			<xsl:attribute name="id">season:<xsl:value-of select="id" /></xsl:attribute>
			<div class="box_title">
				<div class="left">
					Season
					<span class="editNumeric">
						<xsl:attribute name="id"><xsl:value-of
							select="id" />:season</xsl:attribute>
						<xsl:value-of select="season" />
					</span>
				</div>
				<div class="right">
					<xsl:comment></xsl:comment>
					<xsl:if test="torrent[.!='']">
						<a name="seed">
							<xsl:attribute name="href"><xsl:value-of select="/page/seedurl" />/season/<xsl:value-of select="id" /></xsl:attribute>
							<img src="back-forth.gif" alt="Seed torrent." title="Seed torrent."/>
						</a>
					</xsl:if>
					<a name="watched">
						<xsl:attribute name="href"><xsl:value-of select="/page/watchedurl" />/season/<xsl:value-of select="id" /></xsl:attribute>
						
						<xsl:choose>
						  <xsl:when test="watched = 1">
							<img src="exclaim.gif" alt="Unwatch this." title="Unwatch this."/>
						  </xsl:when>
						  <xsl:otherwise>
							<img src="checkmark.gif" alt="Watched this." title="Set as watched."/>
						  </xsl:otherwise>
						</xsl:choose>
					</a>
				</div>
				<div style="clear:both">
					<xsl:comment></xsl:comment>
				</div>
			</div>
			<div class="box_content" style="display: none;">
				<p>
					<label>State:</label>
					<span class="edit">
						<xsl:attribute name="id"><xsl:value-of
							select="id" />:state</xsl:attribute>
						<xsl:value-of select="state" />
					</span>
				</p>
				<xsl:comment>
				<p>
					<label>Watched:</label>
					<span class="editCheckBox">
						<xsl:attribute name="id"><xsl:value-of
							select="id" />:watched</xsl:attribute>
						<xsl:value-of select="watched" />
					</span>
				</p>
				</xsl:comment>
				<p>
					<label>File:</label>
					<span class="edit">
						<xsl:attribute name="id"><xsl:value-of
							select="id" />:file</xsl:attribute>
						<xsl:value-of select="file" />
					</span>
				</p>
				<p>
					<label>Torrent:</label>
					<span class="edit">
						<xsl:attribute name="id"><xsl:value-of
							select="id" />:torrent</xsl:attribute>
						<xsl:value-of select="torrent" />
					</span>
				</p>
				<xsl:apply-templates select="episode" mode="editable">
					<xsl:sort select="episode" data-type="number"/>
				</xsl:apply-templates>
			</div>
		</div>
	</xsl:template>
	
	<xsl:template match="episode" mode="remove">
		<remove>episode:<xsl:value-of select="id" /></remove>
	</xsl:template>
	
	<xsl:template match="episode" mode="list" xmlns="http://www.w3.org/1999/xhtml">
		<div>
			<xsl:attribute name="id">episode:<xsl:value-of select="id" /></xsl:attribute>
			<div class="left">
				<xsl:value-of select="title" />
			</div>
			<div class="right">
				<a name="watched">
					<xsl:attribute name="href"><xsl:value-of select="/page/watchedepisodeurl" />/id/<xsl:value-of select="id" /></xsl:attribute>
					
					<xsl:choose>
					  <xsl:when test="watched = 1">
						<img src="exclaim.gif" alt="Unwatch this." title="Unwatch this."/>
					  </xsl:when>
					  <xsl:otherwise>
						<img src="checkmark.gif" alt="Watched this." title="Set as watched."/>
					  </xsl:otherwise>
					</xsl:choose>
				</a>
			</div>
			<div style="clear:both">
				<xsl:comment></xsl:comment>
			</div>
		</div>
	</xsl:template>
	
	<xsl:template match="episode" mode="editable" xmlns="http://www.w3.org/1999/xhtml">
		<div class="box">
			<xsl:attribute name="id">episode:<xsl:value-of select="id" /></xsl:attribute>
			<div class="box_title">
				<div class="left">S<xsl:value-of select="format-number(season, '00')" />E<span class="editNumeric">
						<xsl:attribute name="id"><xsl:value-of
							select="id" />:episode</xsl:attribute>
						<xsl:value-of select="format-number(episode, '00')" />
					</span>
					:
					<span class="edit">
						<xsl:attribute name="id"><xsl:value-of
							select="id" />:title</xsl:attribute>
						<xsl:value-of select="title" />
					</span>
					[
					<span class="editDropdown">
						<xsl:attribute name="id"><xsl:value-of
							select="id" />:format</xsl:attribute>
						<xsl:value-of select="format" />
					</span>
					]
				</div>
				<div class="right">
					<xsl:comment></xsl:comment>
					<xsl:if test="torrent[.!='']">
						<a name="seed">
							<xsl:attribute name="href"><xsl:value-of select="/page/seedurl" />/episode/<xsl:value-of select="id" /></xsl:attribute>
							<img src="back-forth.gif" alt="Seed torrent." title="Seed torrent."/>
						</a>
					</xsl:if>
					<a name="watched">
						<xsl:attribute name="href"><xsl:value-of select="/page/watchedurl" />/episode/<xsl:value-of select="id" /></xsl:attribute>
						
						<xsl:choose>
						  <xsl:when test="watched = 1">
							<img src="exclaim.gif" alt="Unwatch this." title="Unwatch this."/>
						  </xsl:when>
						  <xsl:otherwise>
							<img src="checkmark.gif" alt="Watched this." title="Set as watched."/>
						  </xsl:otherwise>
						</xsl:choose>
					</a>
				</div>
				<div style="clear:both">
					<xsl:comment></xsl:comment>
				</div>
			</div>
			<div class="box_content" style="display: none;">
				<xsl:comment>
				<p>
					<label>Watched:</label>
					<span class="editCheckBox">
						<xsl:attribute name="id"><xsl:value-of
							select="id" />:watched</xsl:attribute>
						<xsl:value-of select="watched" />
					</span>
				</p>
				</xsl:comment>
				<p>
					<label>File:</label>
					<span class="edit">
						<xsl:attribute name="id"><xsl:value-of
							select="id" />:file</xsl:attribute>
						<xsl:value-of select="file" />
					</span>
				</p>
				<p>
					<label>Torrent:</label>
					<span class="edit">
						<xsl:attribute name="id"><xsl:value-of
							select="id" />:torrent</xsl:attribute>
						<xsl:value-of select="torrent" />
					</span>
				</p>
			</div>
		</div>
	</xsl:template>
	
	<xsl:template match="feeditem" xmlns="http://www.w3.org/1999/xhtml">
		<div>
			<xsl:attribute name="id">feeditem:<xsl:value-of select="id" /></xsl:attribute>
			<div class="left">
				<xsl:value-of select="title" />
			</div>
			<div class="right">
				<a name="watched">
					<xsl:attribute name="href"><xsl:value-of select="/page/downloadurl" />/file/<xsl:value-of select="url" /></xsl:attribute>
					<img src="play.gif" alt="Download torrent." title="Download torrent."/>
				</a>
			</div>
			<div style="clear:both">
				<xsl:comment></xsl:comment>
			</div>
		</div>
	</xsl:template>
	
	<xsl:template name="feedList" xmlns="http://www.w3.org/1999/xhtml">
		<xsl:if test="/page/list"> 
			<div id="feedList">
				<xsl:apply-templates select="/page/list/feeditem">
					<xsl:sort select="publishedDate" data-type="number" order="descending"/>
				</xsl:apply-templates>
			</div>
		</xsl:if>
	</xsl:template>
	
	<xsl:template match="torrent" xmlns="http://www.w3.org/1999/xhtml">
		<div class="box">
			<xsl:attribute name="id">torrent:<xsl:value-of select="id" /></xsl:attribute>
			<div class="box_title">
				<div class="left">
					<xsl:value-of select="file" />
				</div>
				<div class="right">
					<a>
						<xsl:attribute name="href"> <xsl:value-of
							select="removeurl" /> </xsl:attribute>
						remove
					</a>
				</div>
				<div style="clear:both">
					<xsl:comment></xsl:comment>
				</div>
			</div>
			<xsl:if test="path">
				<div class="box_content">
					<select class="type">
						<option value="default">-- Select type</option>
						<option value="serie">Serie</option>
						<option value="season">Season</option>
						<option value="episode">Episode</option>
						<option value="movie">Movie</option>
					</select>
					<img src="arrow-down.gif" alt="Fill all below this." />
					<div class="default" style="display: inline-block">
						<p>Select type.</p>
					</div>
					<div class="serie" style="display: none">
						<form method="post">
							<xsl:attribute name="action"> <xsl:value-of
								select="/page/addserieurl" /> </xsl:attribute>
								
							<label>Name<input type="text" name="name" maxlength="255" size="42"><xsl:attribute name="value"><xsl:value-of select="details/name" /></xsl:attribute></input></label>
							<label>Year<input type="text" name="year" class="editNumeric" maxlength="4" size="4"><xsl:attribute name="value"><xsl:value-of select="details/year" /></xsl:attribute></input></label>
							<label>Url<input type="text" name="url" maxlength="255" size="42"/></label>
							<input type="hidden" name="torrent">
								<xsl:attribute name="value">
									<xsl:value-of select="torrent" />
								</xsl:attribute>
							</input>
							<input type="hidden" name="file">
								<xsl:attribute name="value">
									<xsl:value-of select="fullpath" />
								</xsl:attribute>
							</input>
							<input type="submit" name="submit" value="Save" />
						</form>
					</div>
					<div class="season" style="display: none">
						<form method="post">
							<xsl:attribute name="action"> <xsl:value-of
								select="/page/addseasonurl" /> </xsl:attribute>
								
							<select name="serieid">
								<xsl:comment></xsl:comment>
							</select>
							<img src="arrow-down.gif" alt="Fill all below this." />
							<label>Season<input type="text" name="season" class="editNumeric" maxlength="2" size="2"><xsl:attribute name="value"><xsl:value-of select="details/season" /></xsl:attribute></input></label>
							<img src="arrow-down.gif" alt="Fill all below this with increasing value." />
							<label>State<input type="text" name="state" maxlength="255"/></label>
							<input type="hidden" name="torrent">
								<xsl:attribute name="value">
									<xsl:value-of select="torrent" />
								</xsl:attribute>
							</input>
							<input type="hidden" name="file">
								<xsl:attribute name="value">
									<xsl:value-of select="fullpath" />
								</xsl:attribute>
							</input>
							<input type="submit" name="submit" value="Save" />
						</form>
					</div>
					<div class="episode" style="display: none">
						<form method="post">
							<xsl:attribute name="action"> <xsl:value-of
									select="/page/addepisodeurl" /> </xsl:attribute>
									
							<select name="serieid">
								<xsl:comment></xsl:comment>
							</select>
							<img src="arrow-down.gif" alt="Fill all below this." />
							<label>Season
								<select name="seasonid">
									<xsl:comment></xsl:comment>
								</select>
							</label>
							<img src="arrow-down.gif" alt="Fill all below this." />
							<label>Episode<input type="text" name="episode" class="editNumeric" maxlength="2" size="2"><xsl:attribute name="value"><xsl:value-of select="details/episode" /></xsl:attribute></input></label>
							<img src="arrow-down.gif" alt="Fill all below this with increasing value." />
							<label>Title<input type="text" name="title" maxlength="255" size="32"><xsl:attribute name="value"><xsl:value-of select="details/name" /></xsl:attribute></input></label>
							<label>Format<select name="format"><xsl:comment></xsl:comment></select></label>
							<img src="arrow-down.gif" alt="Fill all below this." />
							<input type="hidden" name="torrent">
								<xsl:attribute name="value">
									<xsl:value-of select="torrent" />
								</xsl:attribute>
							</input>
							<input type="hidden" name="file">
								<xsl:attribute name="value">
									<xsl:value-of select="fullpath" />
								</xsl:attribute>
							</input>
							<input type="submit" name="submit" value="Save" />
						</form>
					</div>
					<div class="movie" style="display: none">
						<form method="post">
							<xsl:attribute name="action"> <xsl:value-of
									select="/page/addmovieurl" /> </xsl:attribute>
									
							<label>Name<input type="text" name="name" maxlength="255" size="32"><xsl:attribute name="value"><xsl:value-of select="details/name" /></xsl:attribute></input></label>
							<label>Year<input type="text" name="year" class="editNumeric" maxlength="4" size="4"><xsl:attribute name="value"><xsl:value-of select="details/year" /></xsl:attribute></input></label>
							<label>Url<input type="text" name="url" maxlength="255" size="32"/></label>
							<label>Format<select name="format"><xsl:comment></xsl:comment></select></label>
							<input type="hidden" name="torrent">
								<xsl:attribute name="value">
									<xsl:value-of select="torrent" />
								</xsl:attribute>
							</input>
							<input type="hidden" name="file">
								<xsl:attribute name="value">
									<xsl:value-of select="fullpath" />
								</xsl:attribute>
							</input>
							<input type="submit" name="submit" value="Save" />
						</form>
					</div>
					<xsl:apply-templates select="path">
						<xsl:sort select="file"/>
					</xsl:apply-templates>
				</div>
			</xsl:if>
		</div>
	</xsl:template>
	
	<xsl:template match="path" xmlns="http://www.w3.org/1999/xhtml">
		<div class="box">
			<xsl:attribute name="id">file:<xsl:value-of select="id" /></xsl:attribute>
			<div class="box_title">
				<div class="left">
					<xsl:value-of select="file" />
				</div>
				<div class="right">
					<a>
						<xsl:attribute name="href"> <xsl:value-of
							select="removeurl" /> </xsl:attribute>
						watched
					</a>
				</div>
				<div style="clear:both">
					<xsl:comment></xsl:comment>
				</div>
			</div>
			<div class="box_content">
				<xsl:if test="path">
					<xsl:attribute name="style">display: none;</xsl:attribute>
				</xsl:if>
				<select class="type">
					<option value="default">-- Select type</option>
					<option value="serie">Serie</option>
					<option value="season">Season</option>
					<option value="episode">Episode</option>
					<option value="movie">Movie</option>
				</select>
				<img src="arrow-down.gif" alt="Fill all below this." />
				<div class="default" style="display: inline-block">
					<p>Select type.</p>
				</div>
				<div class="serie" style="display: none">
					<form method="post">
						<xsl:attribute name="action"> <xsl:value-of
							select="/page/addserieurl" /> </xsl:attribute>
							
						<label>Name<input type="text" name="name" maxlength="255" size="42"><xsl:attribute name="value"><xsl:value-of select="details/name" /></xsl:attribute></input></label>
						<label>Year<input type="text" name="year" class="editNumeric" maxlength="4" size="4"><xsl:attribute name="value"><xsl:value-of select="details/year" /></xsl:attribute></input></label>
						<label>Url<input type="text" name="url" maxlength="255" size="42"/></label>
						<input type="hidden" name="file">
							<xsl:attribute name="value">
								<xsl:value-of select="fullpath" />
							</xsl:attribute>
						</input>
						<input type="submit" name="submit" value="Save" />
					</form>
				</div>
				<div class="season" style="display: none">
					<form method="post">
						<xsl:attribute name="action"> <xsl:value-of
							select="/page/addseasonurl" /> </xsl:attribute>
							
						<select name="serieid">
							<xsl:comment></xsl:comment>
						</select>
						<img src="arrow-down.gif" alt="Fill all below this." />
						<label>Season<input type="text" name="season" class="editNumeric" maxlength="2" size="2"><xsl:attribute name="value"><xsl:value-of select="details/season" /></xsl:attribute></input></label>
						<img src="arrow-down.gif" alt="Fill all below this with increasing value." />
						<label>State<input type="text" name="state" maxlength="255"/></label>
						<input type="hidden" name="file">
							<xsl:attribute name="value">
								<xsl:value-of select="fullpath" />
							</xsl:attribute>
						</input>
						<input type="submit" name="submit" value="Save" />
					</form>
				</div>
				<div class="episode" style="display: none">
					<form method="post">
						<xsl:attribute name="action"> <xsl:value-of
								select="/page/addepisodeurl" /> </xsl:attribute>
								
						<select name="serieid">
							<xsl:comment></xsl:comment>
						</select>
						<img src="arrow-down.gif" alt="Fill all below this." />
						<label>Season
							<select name="seasonid">
								<xsl:comment></xsl:comment>
							</select>
						</label>
						<img src="arrow-down.gif" alt="Fill all below this." />
						<label>Episode<input type="text" name="episode" class="editNumeric" maxlength="2" size="2"><xsl:attribute name="value"><xsl:value-of select="details/episode" /></xsl:attribute></input></label>
						<img src="arrow-down.gif" alt="Fill all below this with increasing value." />
						<label>Title<input type="text" name="title" maxlength="255" size="32"><xsl:attribute name="value"><xsl:value-of select="details/name" /></xsl:attribute></input></label>
						<label>Format<select name="format"><xsl:comment></xsl:comment></select></label>
						<img src="arrow-down.gif" alt="Fill all below this." />
						<input type="hidden" name="file">
							<xsl:attribute name="value">
								<xsl:value-of select="fullpath" />
							</xsl:attribute>
						</input>
						<input type="submit" name="submit" value="Save" />
					</form>
				</div>
				<div class="movie" style="display: none">
					<form method="post">
						<xsl:attribute name="action"> <xsl:value-of
								select="/page/addmovieurl" /> </xsl:attribute>
								
						<label>Name<input type="text" name="name" maxlength="255"><xsl:attribute name="value"><xsl:value-of select="details/name" /></xsl:attribute></input></label>
						<label>Year<input type="text" name="year" class="editNumeric" maxlength="4" size="4"><xsl:attribute name="value"><xsl:value-of select="details/year" /></xsl:attribute></input></label>
						<label>Url<input type="text" name="url" maxlength="255" size="32"/></label>
						<label>Format<select name="format"><xsl:comment></xsl:comment></select></label>
						<input type="hidden" name="file">
							<xsl:attribute name="value">
								<xsl:value-of select="fullpath" />
							</xsl:attribute>
						</input>
						<input type="submit" name="submit" value="Save" />
					</form>
				</div>
				<xsl:if test="path">
					<xsl:apply-templates select="path">
						<xsl:sort select="file"/>
					</xsl:apply-templates>
				</xsl:if>
			</div>
		</div>
	</xsl:template>
	
	<xsl:template match="seek" mode="remove">
		<remove>seek:<xsl:value-of select="id" /></remove>
	</xsl:template>
	
	<xsl:template match="seek" mode="list" xmlns="http://www.w3.org/1999/xhtml">
		<div>
			<xsl:attribute name="id">seek:<xsl:value-of select="id" /></xsl:attribute>
			<div class="left">
				<xsl:value-of select="name" />
			</div>
			<div class="right">
				<p>X</p>
			</div>
			<div style="clear:both">
				<xsl:comment></xsl:comment>
			</div>
		</div>
	</xsl:template>
	
	<xsl:template match="seek" mode="editable" xmlns="http://www.w3.org/1999/xhtml">
		<div class="box">
			<xsl:attribute name="id">seek:<xsl:value-of select="id" /></xsl:attribute>
			<div class="box_title">
				<div class="left">
					<span class="edit">
						<xsl:attribute name="id">name:<xsl:value-of
							select="id" /></xsl:attribute>
						<xsl:value-of select="name" />
					</span>
					(
					<span class="editNumeric">
						<xsl:attribute name="id">year:<xsl:value-of
							select="id" /></xsl:attribute>
						<xsl:value-of select="year" />
					</span>
					)
				</div>
				<div class="right">
					<p>X</p>
				</div>
				<div class="clearer">
					<xsl:comment></xsl:comment>
				</div>
			</div>
			<div class="box_content" style="display: none;">
				<p>
					<label>Url:</label>
					<span class="edit">
						<xsl:attribute name="id">url:<xsl:value-of
							select="id" /></xsl:attribute>
						<xsl:value-of select="url" />
					</span>
				</p>
			</div>
		</div>
	</xsl:template>
	
	<xsl:template name="editableSeekList" xmlns="http://www.w3.org/1999/xhtml">
		<xsl:if test="/page/list"> 
			<div id="editableSeekList">
				<xsl:apply-templates select="/page/list/seek" mode="editable" />
			</div>
		</xsl:if>
	</xsl:template>
</xsl:stylesheet>
