<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns="http://www.loc.gov/MARC21/slim" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" exclude-result-prefixes="dc">
	<xsl:output method="xml" indent="yes" omit-xml-declaration="yes"/>

	<xsl:template match="/">
		<mods>
			<titleInfo>
				<title><xsl:value-of select="//dc:title"/></title>
			</titleInfo>
			<typeOfResource>text</typeOfResource>
			<genre authority="local">book</genre>
			<genre authority="marcgt">book</genre>

			<xsl:for-each select="//dc:contributor">
				<datafield tag="720" ind1="0" ind2="0">
					<subfield code="a">
						<xsl:value-of select="."/>
					</subfield>
					<subfield code="e">collaborator</subfield>
				</datafield>
			</xsl:for-each>

			<xsl:for-each select="//dc:coverage">
				<datafield tag="500" ind1=" " ind2=" ">
					<subfield code="a">
						<xsl:value-of select="."/>
					</subfield>
				</datafield>
			</xsl:for-each>

			<xsl:for-each select="//dc:creator">
				<datafield tag="720" ind1=" " ind2=" ">
					<subfield code="a">
						<xsl:value-of select="."/>
					</subfield>
					<subfield code="e">author</subfield>
				</datafield>
			</xsl:for-each>

			<xsl:for-each select="//dc:date">
				<datafield tag="260" ind1=" " ind2=" ">
					<subfield code="c">
						<xsl:value-of select="."/>
					</subfield>
				</datafield>
			</xsl:for-each>

			<xsl:for-each select="//dc:description">
				<datafield tag="520" ind1=" " ind2=" ">
					<subfield code="a">
						<xsl:value-of select="."/>
					</subfield>
				</datafield>
			</xsl:for-each>

			<xsl:for-each select="//dc:format">
				<datafield tag="856" ind1=" " ind2=" ">
					<subfield code="q">
						<xsl:value-of select="."/>
					</subfield>
				</datafield>
			</xsl:for-each>

			<xsl:for-each select="//dc:identifier">
				<datafield tag="024" ind1="8" ind2=" ">
					<subfield code="a">
						<xsl:value-of select="."/>
					</subfield>
				</datafield>
			</xsl:for-each>

			<xsl:for-each select="//dc:language">
				<datafield tag="546" ind1=" " ind2=" ">
					<subfield code="a">
						<xsl:value-of select="."/>
					</subfield>
				</datafield>
			</xsl:for-each>

			<xsl:for-each select="//dc:publisher">
				<datafield tag="260" ind1=" " ind2=" ">
					<subfield code="b">
						<xsl:value-of select="."/>
					</subfield>
				</datafield>
			</xsl:for-each>

			<xsl:for-each select="//dc:relation">
				<datafield tag="787" ind1="0" ind2=" ">
					<subfield code="n">
						<xsl:value-of select="."/>
					</subfield>
				</datafield>
			</xsl:for-each>

			<xsl:for-each select="//dc:rights">
				<datafield tag="540" ind1=" " ind2=" ">
					<subfield code="a">
						<xsl:value-of select="."/>
					</subfield>
				</datafield>
			</xsl:for-each>

			<xsl:for-each select="//dc:source">
				<datafield tag="786" ind1="0" ind2=" ">
					<subfield code="n">
						<xsl:value-of select="."/>
					</subfield>
				</datafield>
			</xsl:for-each>

			<xsl:for-each select="//dc:subject">
				<datafield tag="653" ind1=" " ind2=" ">
					<subfield code="a">
						<xsl:value-of select="."/>
					</subfield>
				</datafield>
			</xsl:for-each>

			<xsl:for-each select="//dc:title[1]">
				<datafield tag="245" ind1="0" ind2="0">
					<subfield code="a">
						<xsl:value-of select="."/>
					</subfield>
				</datafield>
			</xsl:for-each>



			<xsl:for-each select="//dc:type">
				<datafield tag="655" ind1="7" ind2=" ">
					<subfield code="a">
						<xsl:value-of select="."/>
					</subfield>
					<subfield code="2">local</subfield>
				</datafield>
			</xsl:for-each>
		</mods>
	</xsl:template>
</xsl:stylesheet>
