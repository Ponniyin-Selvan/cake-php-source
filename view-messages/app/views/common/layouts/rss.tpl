<?xml version="1.0" encoding="iso-8859-1"?>
<?xml-stylesheet type="text/css" 
                    href="/css/rss.css" ?>
<rss version="2.0">
  <channel>
    <title>{$group_title} Yahoo Group</title>
    <description>{$rss_description}</description>
    <link>http://{$smarty.server.SERVER_NAME}</link>
    <webMaster>{$webmaster_email}</webMaster>
    <copyright>Copyright 2007, {$group_title} Yahoo Group</copyright>
    <language>en-US</language>
    <pubDate>{$smarty.now|date_format:"%Y-%m-%d %H:%M:%S"}</pubDate>
    <generator>Cake PHP 1.2</generator>
    <docs>http://cyber.law.harvard.edu/rss/rss.html</docs>
 {if (!empty($messages))}
       {$rss->items($messages, 'transformRSS')}
 {/if}

 {php}
 function transformRSS($messages) {
    return array(
           'title' => '#'.$messages['Post']['id'].' '.$messages['Post']['thread_topic'],
           'link' => array('url'=> $messages['Post']['url']),
           'comments' => array('url'=> $messages['Post']['url'].'#comments'),
           'guid' => array('url'=> $messages['Post']['url']),
           'pubDate' => date('r', strtotime($messages['Post']['posted_on'])),
           'author' => $messages['Post']['member_name']."(".$messages['Post']['member'].")",
           'description' => $messages['Post']['formatted_message']
    );
 }
 {/php}
  </channel>
</rss>
