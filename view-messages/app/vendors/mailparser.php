<?php 
vendor('eZ/Base/src/base');

// Required method to be able to use the eZ Components
function __autoload( $className )
{
        ezcBase::autoload( $className );
}

class MailParser {

	function parse($source) {
		$parser = new ezcMailParser();
    	$set = new ezcMailVariableSet( $source );
    	$mail = $parser->parseMail( $set );
    	return $mail[0];
	}

function formatMail( $mail )
{
    $t = '';
    $t .= "From:      ". $this->formatAddress( $mail->from). "\n";
    $t .= "To:        ". $this->formatAddresses( $mail->to ). "\n";
    $t .= "Cc:        ". $this->formatAddresses( $mail->cc ). "\n";
    $t .= "Bcc:       ". $this->formatAddresses( $mail->bcc ). "\n";
    $t .= 'Date:      '. date( DATE_RFC822, $mail->timestamp ). "\n";
    $t .= 'Subject:   '. $mail->subject . "\n";
    $t .= "MessageId: ". $mail->messageId . "\n";
    $t .= "\n";
    $t .= $this->formatMailPart( $mail->body );
    return $t;
}

function formatMailPart( $part )
{
    if ( $part instanceof ezcMail )
        return $this->formatMail( $part );

    if ( $part instanceof ezcMailText )
        return $this->formatMailText( $part );

    if ( $part instanceof ezcMailFile )
        return $this->formatMailFile( $part );

    if ( $part instanceof ezcMailRfc822Digest )
        return $this->formatMailRfc822Digest( $part );

    if ( $part instanceof ezcMailMultiPart )
        return $this->formatMailMultipart( $part );

    die( "No clue about the ". get_class( $part ) . "\n" );
}

function formatMailMultipart( $part )
{
    if ( $part instanceof ezcMailMultiPartAlternative )
        return $this->formatMailMultipartAlternative( $part );

    if ( $part instanceof ezcMailMultiPartDigest )
        return $this->formatMailMultipartDigest( $part );

    if ( $part instanceof ezcMailMultiPartRelated )
        return $this->formatMailMultipartRelated( $part );

    if ( $part instanceof ezcMailMultiPartMixed )
        return $this->formatMailMultipartMixed( $part );

    die( "No clue about the ". get_class( $part ) . "\n" );
}

function formatMailMultipartMixed( $part )
{
    $t = '';
    foreach ( $part->getParts() as $key => $alternativePart )
    {
        $t .= "-MIXED-$key------------------------------------------------------------------\n";
        $t .= $this->formatMailPart( $alternativePart );
    }
    $t .= "-MIXED END----------------------------------------------------------\n";
    return $t;
}

function formatMailMultipartRelated( $part )
{
    $t = '';
    $t .= "-RELATED MAIN PART-----------------------------------------------------------\n";
    $t .= $this->formatMailPart( $part->getMainPart() );
    foreach ( $part->getRelatedParts() as $key => $alternativePart )
    {
        $t .= "-RELATED PART $key-----------------------------------------------------\n";
        $t .= $this->formatMailPart( $alternativePart );
    }
    $t .= "-RELATED END--------------------------------------------------------\n";
    return $t;
}

function formatMailMultipartDigest( $part )
{
    $t = '';
    foreach ( $part->getParts() as $key => $alternativePart )
    {
        $t .= "-DIGEST-$key-----------------------------------------------------------------\n";
        $t .= $this->formatMailPart( $alternativePart );
    }
    $t .= "-DIGEST END---------------------------------------------------------\n";
    return $t;
}

function formatMailRfc822Digest( $part )
{
    $t = '';
    $t .= "-DIGEST-ITEM-$key------------------------------------------------------------\n";
    $t .= "Item:\n\n";
    $t .= $this->formatMailpart( $part->mail );
    $t .= "-DIGEST ITEM END----------------------------------------------------\n";
    return $t;
}

function formatMailMultipartAlternative( $part )
{
    $t = '';
    foreach ( $part->getParts() as $key => $alternativePart )
    {
        $t .= "-ALTERNATIVE ITEM $key-------------------------------------------------------\n";
        $t .= $this->formatMailPart( $alternativePart );
    }
    $t .= "-ALTERNATIVE END----------------------------------------------------\n";
    return $t;
}

function formatMailText( $part )
{
    $t = '';
    $t .= "Original Charset: {$part->originalCharset}\n";
    $t .= "Charset:          {$part->charset}\n";
    $t .= "Encoding:         {$part->encoding}\n";
    $t .= "Type:             {$part->subType}\n";
    $t .= "\n{$part->text}\n";
    return $t;
}

function formatMailFile( $part )
{
    $t = '';
    $t .= "Disposition Type: {$part->dispositionType}\n";
    $t .= "Content Type:     {$part->contentType}\n";
    $t .= "Mime Type:        {$part->mimeType}\n";
    $t .= "Content ID:       {$part->contentId}\n";
    $t .= "Filename:         {$part->fileName}\n";
    $t .= "\n";
    return $t;
}

function formatAddresses( $addresses )
{
    $fa = array();
    foreach ( $addresses as $address )
    {
        $fa[] = $this->formatAddress( $address );
    }
    return implode( ', ', $fa );
}

function formatAddress( $address )
{
    $name = '';
    if ( !empty( $address->name ) )
    {
        $name = "{$address->name} ";
    }
    return $name. "<{$address->email}>";    
}
}
?>