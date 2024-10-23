<?php echo '<?xml version="1.0" encoding="UTF-8"?>'."\n"; ?>
<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/">
 <ShortName>search chat.indieweb.org</ShortName>
 <Description>Search</Description>
 <Url type="application/atom+xml" rel="results"
      template="https://indiechat.search.cweiske.de/?q={searchTerms}&amp;page={startPage?}&amp;format=opensearch"/>
 <Url type="text/html" rel="results" method="get"
      template="https://indiechat.search.cweiske.de/?q={searchTerms}"/>
</OpenSearchDescription>
