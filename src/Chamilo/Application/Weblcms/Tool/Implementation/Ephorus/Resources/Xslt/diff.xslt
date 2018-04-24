<?xml version="1.0" encoding="utf-8"?>

<xsl:stylesheet
        version="1.0"
        xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
>
    <xsl:strip-space elements="*" />
    <xsl:output
        method="html"
        media-type="text/html"
        encoding="UTF-8" />
    <xsl:decimal-format
        name="european"
        decimal-separator=','
        grouping-separator='.' />

    <xsl:param name="filter">
        1
    </xsl:param>
    <xsl:param name="title"></xsl:param>
    <xsl:param name="result_id"></xsl:param>
    <xsl:param name="original"></xsl:param>
    <xsl:param name="found"></xsl:param>
    <xsl:param name="match">
        60
    </xsl:param>
    <xsl:param name="authenticity">
        60
    </xsl:param>
    <xsl:param name="words">
        9
    </xsl:param>
    <xsl:param name="TotalCharacters">
        0
    </xsl:param>
    <xsl:param name="to_next_result"></xsl:param>
    <xsl:param name="to_document_beg"></xsl:param>

    <xsl:variable name="check_match_1">
        95
    </xsl:variable>
    <xsl:variable name="check_match_2">
        90
    </xsl:variable>
    <xsl:variable name="check_match_3">
        80
    </xsl:variable>
    <xsl:variable name="check_match_4">
        70
    </xsl:variable>
    <xsl:variable name="check_match_5">
        <xsl:value-of select="$match" />
    </xsl:variable>
    <xsl:variable name="check_match">
        <xsl:choose>
            <xsl:when test="$filter=1">
                <xsl:value-of select="$check_match_1" />
            </xsl:when>
            <xsl:when test="$filter=2">
                <xsl:value-of select="$check_match_2" />
            </xsl:when>
            <xsl:when test="$filter=3">
                <xsl:value-of select="$check_match_3" />
            </xsl:when>
            <xsl:when test="$filter=4">
                <xsl:value-of select="$check_match_4" />
            </xsl:when>
            <xsl:when test="$filter=5">
                <xsl:value-of select="$check_match_5" />
            </xsl:when>
        </xsl:choose>
    </xsl:variable>

    <xsl:variable name="check_authenticity_1">
        95
    </xsl:variable>
    <xsl:variable name="check_authenticity_2">
        90
    </xsl:variable>
    <xsl:variable name="check_authenticity_3">
        80
    </xsl:variable>
    <xsl:variable name="check_authenticity_4">
        70
    </xsl:variable>
    <xsl:variable name="check_authenticity_5">
        <xsl:value-of select="$authenticity" />
    </xsl:variable>
    <xsl:variable name="check_authenticity">
        <xsl:choose>
            <xsl:when test="$filter=1">
                <xsl:value-of select="$check_authenticity_1" />
            </xsl:when>
            <xsl:when test="$filter=2">
                <xsl:value-of select="$check_authenticity_2" />
            </xsl:when>
            <xsl:when test="$filter=3">
                <xsl:value-of select="$check_authenticity_3" />
            </xsl:when>
            <xsl:when test="$filter=4">
                <xsl:value-of select="$check_authenticity_4" />
            </xsl:when>
            <xsl:when test="$filter=5">
                <xsl:value-of select="$check_authenticity_5" />
            </xsl:when>
        </xsl:choose>
    </xsl:variable>

    <xsl:variable name="check_words_1">
        75
    </xsl:variable>
    <xsl:variable name="check_words_2">
        50
    </xsl:variable>
    <xsl:variable name="check_words_3">
        30
    </xsl:variable>
    <xsl:variable name="check_words_4">
        20
    </xsl:variable>
    <xsl:variable name="check_words_5">
        <xsl:value-of select="$words" />
    </xsl:variable>
    <xsl:variable name="check_words">
        <xsl:choose>
            <xsl:when test="$filter=1">
                <xsl:value-of select="$check_words_1" />
            </xsl:when>
            <xsl:when test="$filter=2">
                <xsl:value-of select="$check_words_2" />
            </xsl:when>
            <xsl:when test="$filter=3">
                <xsl:value-of select="$check_words_3" />
            </xsl:when>
            <xsl:when test="$filter=4">
                <xsl:value-of select="$check_words_4" />
            </xsl:when>
            <xsl:when test="$filter=5">
                <xsl:value-of select="$check_words_5" />
            </xsl:when>
        </xsl:choose>
    </xsl:variable>

    <xsl:variable name="result_count">
        <xsl:choose>
            <xsl:when test="$filter=1">
                <xsl:value-of
                    select="count(/document/result[@authenticity &gt;= $check_authenticity_1 and @words &gt; $check_words_1 and @match &gt;= $check_match_1])" />
            </xsl:when>
            <xsl:when test="$filter=2">
                <xsl:value-of
                    select="count(/document/result[@authenticity &gt;= $check_authenticity_2 and @words &gt; $check_words_2 and @match &gt;= $check_match_2])" />
            </xsl:when>
            <xsl:when test="$filter=3">
                <xsl:value-of
                    select="count(/document/result[@authenticity &gt;= $check_authenticity_3 and @words &gt; $check_words_3 and @match &gt;= $check_match_3])" />
            </xsl:when>
            <xsl:when test="$filter=4">
                <xsl:value-of
                    select="count(/document/result[@authenticity &gt;= $check_authenticity_4 and @words &gt; $check_words_4 and @match &gt;= $check_match_4])" />
            </xsl:when>
            <xsl:when test="$filter=5">
                <xsl:value-of
                    select="count(/document/result[@authenticity &gt;= $check_authenticity_5 and @words &gt; $check_words_5 and @match &gt;= $check_match_5])" />
            </xsl:when>
        </xsl:choose>
    </xsl:variable>

    <xsl:template match="/document">
        <table
            border="0"
            width="100%"
            cellpadding="0"
            cellspacing="0">
            <xsl:apply-templates />
        </table>
    </xsl:template>

    <xsl:template match="fragment">
        <xsl:variable name="fragment_nr">
            <xsl:number
                count="fragment"
                level="any" />
        </xsl:variable>
        <xsl:variable name="result_nr">
            <xsl:choose>
                <xsl:when test="$filter=1">
                    <xsl:number
                        count="result[@authenticity &gt;= $check_authenticity_1 and @words &gt; $check_words_1 and @match &gt;= $check_match_1]"
                        level="any" />
                </xsl:when>
                <xsl:when test="$filter=2">
                    <xsl:number
                        count="result[@authenticity &gt;= $check_authenticity_2 and @words &gt; $check_words_2 and @match &gt;= $check_match_2]"
                        level="any" />
                </xsl:when>
                <xsl:when test="$filter=3">
                    <xsl:number
                        count="result[@authenticity &gt;= $check_authenticity_3 and @words &gt; $check_words_3 and @match &gt;= $check_match_3]"
                        level="any" />
                </xsl:when>
                <xsl:when test="$filter=4">
                    <xsl:number
                        count="result[@authenticity &gt;= $check_authenticity_4 and @words &gt; $check_words_4 and @match &gt;= $check_match_4]"
                        level="any" />
                </xsl:when>
                <xsl:when test="$filter=5">
                    <xsl:number
                        count="result[@authenticity &gt;= $check_authenticity_5 and @words &gt; $check_words_5 and @match &gt;= $check_match_5]"
                        level="any" />
                </xsl:when>
            </xsl:choose>
        </xsl:variable>
        <xsl:variable name="result_NR">
            <xsl:choose>
                <xsl:when test="$filter=1">
                    <xsl:value-of
                        select="count(/document/result[@authenticity &gt;= $check_authenticity_1 and @words &gt; $check_words_1 and @match &gt;= $check_match_1])" />
                </xsl:when>
                <xsl:when test="$filter=2">
                    <xsl:value-of
                        select="count(/document/result[@authenticity &gt;= $check_authenticity_2 and @words &gt; $check_words_2 and @match &gt;= $check_match_2])" />
                </xsl:when>
                <xsl:when test="$filter=3">
                    <xsl:value-of
                        select="count(/document/result[@authenticity &gt;= $check_authenticity_3 and @words &gt; $check_words_3 and @match &gt;= $check_match_3])" />
                </xsl:when>
                <xsl:when test="$filter=4">
                    <xsl:value-of
                        select="count(/document/result[@authenticity &gt;= $check_authenticity_4 and @words &gt; $check_words_4 and @match &gt;= $check_match_4])" />
                </xsl:when>
                <xsl:when test="$filter=5">
                    <xsl:value-of
                        select="count(/document/result[@authenticity &gt;= $check_authenticity_5 and @words &gt; $check_words_5 and @match &gt;= $check_match_5])" />
                </xsl:when>
            </xsl:choose>
        </xsl:variable>

        <tr>
            <td valign="top">
                <!-- Als fragment leeg is geen button tonen -->
                <xsl:if test="text()[normalize-space(.)]">
                    <!-- Als er geen resultaten zijn geen button tonen -->
                    <xsl:if test="$result_count>0">
                        <!-- Als het result_nr gelijk is aan het aant. resultaten linken naar #top -->

                    </xsl:if>
                </xsl:if>
            </td>
            <td>
                <p class="summary_origineel">
                    <xsl:call-template name="replace-string">
                        <xsl:with-param
                            name="text"
                            select="." />
                        <xsl:with-param
                            name="from"
                            select="'&#10;'" />
                        <xsl:with-param
                            name="to"
                            select="'&lt;br/&gt;'" />
                    </xsl:call-template>
                </p>
            </td>
        </tr>
    </xsl:template>

    <xsl:template match="result">
        <xsl:variable name="fragment_nr">
            <xsl:number
                count="fragment"
                level="any"></xsl:number>
        </xsl:variable>
        <xsl:variable name="result_nr">
            <xsl:choose>
                <xsl:when test="$filter=1">
                    <xsl:number
                        count="result[@authenticity &gt;= $check_authenticity_1 and @words &gt; $check_words_1 and @match &gt;= $check_match_1]"
                        level="any" />
                </xsl:when>
                <xsl:when test="$filter=2">
                    <xsl:number
                        count="result[@authenticity &gt;= $check_authenticity_2 and @words &gt; $check_words_2 and @match &gt;= $check_match_2]"
                        level="any" />
                </xsl:when>
                <xsl:when test="$filter=3">
                    <xsl:number
                        count="result[@authenticity &gt;= $check_authenticity_3 and @words &gt; $check_words_3 and @match &gt;= $check_match_3]"
                        level="any" />
                </xsl:when>
                <xsl:when test="$filter=4">
                    <xsl:number
                        count="result[@authenticity &gt;= $check_authenticity_4 and @words &gt; $check_words_4 and @match &gt;= $check_match_4]"
                        level="any" />
                </xsl:when>
                <xsl:when test="$filter=5">
                    <xsl:number
                        count="result[@authenticity &gt;= $check_authenticity_5 and @words &gt; $check_words_5 and @match &gt;= $check_match_5]"
                        level="any" />
                </xsl:when>
            </xsl:choose>
        </xsl:variable>
        <tr>
            <td valign="top">
                <!-- Als er geen resultaten zijn geen button tonen -->
                <xsl:if test="$result_count>0">

                </xsl:if>
            </td>
            <td>
                <xsl:choose>
                    <xsl:when test="@authenticity &gt;= $check_authenticity and @words &gt;= $check_words and @match &gt;= $check_match">
                        <table
                            border="0"
                            cellpadding="0"
                            cellspacing="0"
                            width="100%">
                            <tr>
                                <td height="4"></td>
                                <td height="4"></td>
                            </tr>
                            <tr>
                                <td
                                    class="result_header_origineel"
                                    style="word-wrap:break-word; width: 50%;">
                                    <xsl:value-of
                                        select="$original"
                                        disable-output-escaping="yes" />
                                </td>
                                <td
                                    class="result_header_vergelijk"
                                    style="word-wrap:break-word; width: 50%;">
                                    <xsl:value-of
                                        select="$found"
                                        disable-output-escaping="yes" />
                                </td>
                            </tr>
                            <tr>
                                <td
                                    valign="top"
                                    class="result_origineel"
                                    style="word-wrap:break-word; width: 50%;">
                                    <xsl:apply-templates select="origineel | addedword" />
                                </td>
                                <td
                                    valign="top"
                                    class="result_vergelijk"
                                    style="word-wrap:break-word; width: 50%;">
                                    <xsl:apply-templates select="vergelijk | removedword" />
                                </td>
                            </tr>
                            <tr>
                                <td height="4"></td>
                                <td height="4"></td>
                            </tr>
                            <tr>
                            </tr>
                        </table>
                    </xsl:when>
                    <xsl:otherwise>
                        <table
                            border="0"
                            cellpadding="0"
                            cellspacing="0"
                            width="100%">
                            <tr>
                                <td style="width: 100%; word-wrap:break-word;">
                                    <p>
                                        <xsl:apply-templates select="origineel" />
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </xsl:otherwise>
                </xsl:choose>
            </td>
        </tr>
    </xsl:template>

    <xsl:template match="origineel">
        <xsl:call-template name="replace-string">
            <xsl:with-param
                name="text"
                select="." />
            <xsl:with-param
                name="from"
                select="'&#10;'" />
            <xsl:with-param
                name="to"
                select="'&lt;br/&gt;'" />
        </xsl:call-template>
    </xsl:template>

    <xsl:template match="vergelijk">
        <xsl:call-template name="replace-string">
            <xsl:with-param
                name="text"
                select="." />
            <xsl:with-param
                name="from"
                select="'&#10;'" />
            <xsl:with-param
                name="to"
                select="'&lt;br/&gt;'" />
        </xsl:call-template>
    </xsl:template>

    <xsl:template match="addedword">
        <font class="summary_added">
            <xsl:call-template name="replace-string">
                <xsl:with-param
                    name="text"
                    select="." />
                <xsl:with-param
                    name="from"
                    select="'&#10;'" />
                <xsl:with-param
                    name="to"
                    select="'&lt;br/&gt;'" />
            </xsl:call-template>

        </font>
    </xsl:template>

    <xsl:template match="removedword">
        <font class="summary_removed">
            <xsl:call-template name="replace-string">
                <xsl:with-param
                    name="text"
                    select="." />
                <xsl:with-param
                    name="from"
                    select="'&#10;'" />
                <xsl:with-param
                    name="to"
                    select="'&lt;br/&gt;'" />
            </xsl:call-template>
        </font>
    </xsl:template>

    <!--SUPPRESS NOT HANDLED ELEMENTS -->
    <xsl:template match="*">
    </xsl:template>

    <xsl:template name="br">
        <br />
    </xsl:template>

    <xsl:template name="replace-string">
        <xsl:param name="text" />
        <xsl:param name="from" />
        <xsl:param name="to" />

        <xsl:choose>
            <xsl:when test="contains($text, $from)">

                <xsl:variable
                    name="before"
                    select="substring-before($text, $from)" />
                <xsl:variable
                    name="after"
                    select="substring-after($text, $from)" />
                <xsl:variable
                    name="prefix"
                    select="concat($before, $to)" />

                <xsl:value-of
                    select="$before"
                    disable-output-escaping="yes" />
                <xsl:value-of
                    select="$to"
                    disable-output-escaping="yes" />
                <xsl:call-template name="replace-string">
                    <xsl:with-param
                        name="text"
                        select="$after" />
                    <xsl:with-param
                        name="from"
                        select="$from" />
                    <xsl:with-param
                        name="to"
                        select="$to" />
                </xsl:call-template>
            </xsl:when>
            <xsl:otherwise>
                <xsl:value-of
                    select="$text"
                    disable-output-escaping="yes" />
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

</xsl:stylesheet>