<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns="http://www.w3.org/1999/xhtml"
		xmlns:xlink="http://www.w3.org/1999/xlink"
								xmlns:tbx="http://www.lisa.org/TBX-Specification.33.0.html"
		xmlns:iso="http://www.iso.org/ns/1.0"
		xmlns:cals="http://www.oasis-open.org/specs/tm9901"
								xmlns:html="http://www.w3.org/1999/xhtml"
								xmlns:teix="http://www.tei-c.org/ns/Examples"
								xmlns:s="http://www.ascc.net/xml/schematron"
								xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
								xmlns:tei="http://www.tei-c.org/ns/1.0"
								xmlns:t="http://www.thaiopensource.com/ns/annotations"
								xmlns:a="http://relaxng.org/ns/compatibility/annotations/1.0"
								xmlns:rng="http://relaxng.org/ns/structure/1.0"
								exclude-result-prefixes="tei html t a rng s iso tbx
					 cals xlink teix"
								version="2.0">
		<xsl:import href="/usr/share/xml/tei/stylesheet/epub/tei-to-epub.xsl"/>

	<doc xmlns="http://www.oxygenxml.com/ns/doc/xsl" scope="stylesheet" type="stylesheet">
			<desc>
				 <p>__PORTALDEFAULT__ profile</p>
			</desc>
	 </doc>

		<xsl:param name="numberHeadings">false</xsl:param>
		<xsl:param name="numberHeadingsDepth">-1</xsl:param>
		<xsl:param name="numberBackHeadings"></xsl:param>
		<xsl:param name="numberFrontHeadings"></xsl:param>
		<xsl:param name="numberFigures">false</xsl:param>
		<xsl:param name="numberTables">false</xsl:param>
		<xsl:param name="autoToc">true</xsl:param>

		<xsl:template match="tei:pb">
			<xsl:choose>
				<xsl:when test="ed='temoin'">
					<span class="d_pb_temoin"> [<xsl:value-of select="@n"/>] </span>
				</xsl:when>
				<xsl:otherwise>
					<span class="d_pb"> [p. <xsl:value-of select="@n"/>] </span>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:template>

		<xsl:template match="tei:l">
			<xsl:choose>
				<xsl:when test="@n">
					<div class="l" title="{@n}"><xsl:apply-templates/></div>
				</xsl:when>
				<xsl:otherwise>
					<div class="l"><xsl:apply-templates/></div>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:template>


</xsl:stylesheet>
