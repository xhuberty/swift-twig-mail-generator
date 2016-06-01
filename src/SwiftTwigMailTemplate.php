<?php

namespace TheCodingMachine\Mail\Template;


use TheCodingMachine\Mail\SwiftMailTemplate;

class SwiftTwigMailTemplate implements SwiftMailTemplate
{
    /**
     * @var \Twig_Environment
     */
    protected $twigEnvironment;

    /**
     * @var string
     */
    protected $twigPath;

    /**
     * @var string|array
     */
    protected $fromAddresses;

    /**
     * @var string
     */
    protected $fromName = null;

    /**
     * @var string|array
     */
    protected $toAddresses;

    /**
     * @var string
     */
    protected $toName = null;

    /**
     * @var string|array
     */
    protected $bccAddresses;

    /**
     * @var string
     */
    protected $bccName = null;

    /**
     * @var string|array
     */
    protected $ccAddresses;

    /**
     * @var string
     */
    protected $ccName = null;

    /**
     * @var string|array
     */
    protected $replyToAddresses;

    /**
     * @var string
     */
    protected $replyToName = null;

    /**
     * @var int
     */
    protected $maxLineLength = 1000;

    /**
     * @var int
     */
    protected $priority;

    /**
     * @var string
     */
    protected $readReceiptTo;

    /**
     * @var string
     */
    protected $returnPath;

    /**
     * SwiftTwigMailGenerator constructor.
     *
     * @param \Twig_Environment $twig_Environment
     * @param string            $twigPath
     */
    public function __construct(\Twig_Environment $twig_Environment, string $twigPath)
    {
        $this->twigEnvironment = $twig_Environment;
        $this->twigPath = $twigPath;
    }

    /**
     * @param array $data
     *
     * @return \Swift_Message
     */
    public function renderMail(array $data = []) :\Swift_Message
    {
        $mail = new \Swift_Message();

        $twigEnvironment = clone $this->twigEnvironment;
        $function = new \Twig_SimpleFunction('embedImage', function ($imgPath) use ($mail) {
            return $mail->embed(\Swift_Image::fromPath($imgPath));
        });
        $twigEnvironment->addFunction($function);

        $template = $twigEnvironment->loadTemplate($this->twigPath);

        if (!$template->hasBlock('subject') || !$template->hasBlock('body_html')) {
            throw MissingBlockException::missingBlock($template->getBlockNames());
        }

        $subject  = $template->renderBlock('subject', $data);
        $bodyHtml = $template->renderBlock('body_html', $data);
        if (!$template->hasBlock('body_text')) {
            $bodyText = $this->removeHtml($bodyHtml);
        } else {
            $bodyText = $template->renderBlock('body_text', $data);

        }

        $mail->setSubject($subject);
        $mail->setBody($bodyHtml);
        $mail->addPart($bodyText);

        if ($this->fromAddresses) {
            $mail->setFrom($this->fromAddresses, $this->fromName);
            $mail->setSender($this->fromAddresses, $this->fromName);
        }

        if ($this->toAddresses) {
            $mail->setTo($this->toAddresses, $this->toName);
        }

        if ($this->bccAddresses) {
            $mail->setBcc($this->bccAddresses, $this->bccName);
        }
        if ($this->ccAddresses) {
            $mail->setCc($this->ccAddresses, $this->ccName);
        }
        if ($this->replyToAddresses) {
            $mail->setReplyTo($this->replyToAddresses, $this->replyToName);
        }

        if ($this->maxLineLength) {
            $mail->setMaxLineLength($this->maxLineLength);
        }
        if ($this->priority) {
            $mail->setPriority($this->priority);
        }

        if ($this->readReceiptTo) {
            $mail->setReadReceiptTo($this->readReceiptTo);
        }

        if ($this->returnPath) {
            $mail->setReturnPath($this->returnPath);
        }

        return $mail;
    }

    /**
     * @param array|string $fromAddresses
     */
    public function setFromAddresses($fromAddresses)
    {
        $this->fromAddresses = $fromAddresses;
    }

    /**
     * @param string $fromName
     */
    public function setFromName($fromName)
    {
        $this->fromName = $fromName;
    }

    /**
     * @param array|string $toAddresses
     */
    public function setToAddresses($toAddresses)
    {
        $this->toAddresses = $toAddresses;
    }

    /**
     * @param string $toName
     */
    public function setToName($toName)
    {
        $this->toName = $toName;
    }

    /**
     * @param array|string $bccAddresses
     */
    public function setBccAddresses($bccAddresses)
    {
        $this->bccAddresses = $bccAddresses;
    }

    /**
     * @param string $bccName
     */
    public function setBccName($bccName)
    {
        $this->bccName = $bccName;
    }

    /**
     * @param array|string $ccAddresses
     */
    public function setCcAddresses($ccAddresses)
    {
        $this->ccAddresses = $ccAddresses;
    }

    /**
     * @param string $ccName
     */
    public function setCcName($ccName)
    {
        $this->ccName = $ccName;
    }

    /**
     * @param array|string $replyToAddresses
     */
    public function setReplyToAddresses($replyToAddresses)
    {
        $this->replyToAddresses = $replyToAddresses;
    }

    /**
     * @param string $replyToName
     */
    public function setReplyToName($replyToName)
    {
        $this->replyToName = $replyToName;
    }

    /**
     * @param int $maxLineLength
     */
    public function setMaxLineLength($maxLineLength)
    {
        $this->maxLineLength = $maxLineLength;
    }

    /**
     * @param int $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * @param string $readReceiptTo
     */
    public function setReadReceiptTo($readReceiptTo)
    {
        $this->readReceiptTo = $readReceiptTo;
    }

    /**
     * @param string $returnPath
     */
    public function setReturnPath($returnPath)
    {
        $this->returnPath = $returnPath;
    }

    /**
     * Removes the HTML tags from the text.
     *
     * @param string $s
     * @param string $keep The list of tags to keep
     * @param string $expand The list of tags to remove completely, along their content
     */
    private function removeHtml(string $s , string $keep = '' , string $expand = 'script|style|noframes|select|option') :string
    {
        /**///prep the string
        $s = ' ' . $s;

        /**///initialize keep tag logic
        if(strlen($keep) > 0){
            $k = explode('|',$keep);
            for($i=0;$i<count($k);$i++){
                $s = str_replace('<' . $k[$i],'[{(' . $k[$i],$s);
                $s = str_replace('</' . $k[$i],'[{(/' . $k[$i],$s);
            }
        }
        $pos = array();
        $len = array();

        //begin removal
        /**///remove comment blocks
        while(stripos($s,'<!--') > 0){
            $pos[1] = stripos($s,'<!--');
            $pos[2] = stripos($s,'-->', $pos[1]);
            $len[1] = $pos[2] - $pos[1] + 3;
            $x = substr($s,$pos[1],$len[1]);
            $s = str_replace($x,'',$s);
        }

        /**///remove tags with content between them
        if(strlen($expand) > 0){
            $e = explode('|',$expand);
            for($i=0;$i<count($e);$i++){
                while(stripos($s,'<' . $e[$i]) > 0){
                    $len[1] = strlen('<' . $e[$i]);
                    $pos[1] = stripos($s,'<' . $e[$i]);
                    $pos[2] = stripos($s,$e[$i] . '>', $pos[1] + $len[1]);
                    $len[2] = $pos[2] - $pos[1] + $len[1];
                    $x = substr($s,$pos[1],$len[2]);
                    $s = str_replace($x,'',$s);
                }
            }
        }

        /**///remove remaining tags
        while(stripos($s,'<') > 0){
            $pos[1] = stripos($s,'<');
            $pos[2] = stripos($s,'>', $pos[1]);
            $len[1] = $pos[2] - $pos[1] + 1;
            $x = substr($s,$pos[1],$len[1]);
            $s = str_replace($x,'',$s);
        }

        /**///finalize keep tag
        if (isset($k)) {
            for($i=0;$i<count($k);$i++){
                $s = str_replace('[{(' . $k[$i],'<' . $k[$i],$s);
                $s = str_replace('[{(/' . $k[$i],'</' . $k[$i],$s);
            }
        }

        return trim($s);
    }
}
