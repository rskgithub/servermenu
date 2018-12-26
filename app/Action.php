<?php

namespace ServerMenu;


class Action
{
    /**
     * @var string
     */
    private $title;
    /**
     * @var PluginBase
     */
    private $pluginType;
    /**
     * @var string
     */
    private $receiverType;
    /**
     * @var string
     */
    private $content;
    /**
     * @var string
     */
    private $glyphIcon;

    public function __construct($title, PluginBase $pluginType, $receiverType, $content, $glyphIcon)
    {
        $this->title = $title;
        $this->pluginType = $pluginType;
        $this->receiverType = $receiverType;
        $this->content = $content;
        $this->glyphIcon = $glyphIcon;
    }

    /**
     * @return string
     */
    public function getGlyphIcon()
    {
        return $this->glyphIcon;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return PluginBase
     */
    public function getPluginType()
    {
        return $this->pluginType;
    }

    /**
     * @return string
     */
    public function getReceiverType()
    {
        return $this->receiverType;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
}
