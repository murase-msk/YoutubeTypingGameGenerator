<?php
/**
 * Created by PhpStorm.
 * User: masaki
 * Date: 2019/01/13
 * Time: 17:08
 */

namespace src\Model\typingGame;

use src\Model\TypingGameModel;

/**
 * Youtube動画のバリデーションチェックをする
 * Class ValidationVideo
 * @package src\Model\typingGame
 */
class ValidationVideo
{
    /** YoutubeのVideoId */
    public $videoId;
    /** Youtube動画のURLであるか */
    public $isYoutubeUrl;

    public $langListIndex;

    /**
     * ValidationVideo constructor.
     * @param string $url youtubeの動画URL
     * @param string $type
     */
    public function __construct($url, $type = 'youtubeUrl')
    {
        if ($type == 'youtubeUrl') {
            $isMatch = preg_match(
                '/^(http|https):\/\/(www\.youtube\.com\/watch\?v=)([A-Z0-9_-]+)(&.*)?/i',
                $url,
                $matchResult
            );
            $this->videoId = $matchResult[3];
            $this->isYoutubeUrl = $isMatch === 1;
        } else if ($type == 'videoId') {
            $this->videoId = $url;
            $this->isYoutubeUrl = true;
        } else {
            throw new Exception('正しくありません');
        }
    }

    /**
     * 動画登録できるかチェックする
     * @param TypingGameModel $typingGameModel
     * @param string $languageType
     * @param ScrappingTypeText $scrappingTypeText
     * @return array チェックの結果を返す['result'=>..., 'msg'=>...]
     */
    public function validateUrl(
        TypingGameModel $typingGameModel,
        string $languageType,
        ScrappingTypeText $scrappingTypeText
    ): array
    {
        $result = ['result' => 'ok', 'msg' => ''];
        if (!$this->isMatchYoutubeUrl()) {
            $result['result'] = 'error';
            $result['msg'] = 'URLが正しくありません';
        } else if ($this->isAlreadyRegisteredUrl($typingGameModel)) {
            $result['result'] = 'redirect';
            $result['msg'] = 'すでに登録されています';
        } else if (!$this->isExistLanguageScript($languageType, $scrappingTypeText)) {
            $result['result'] = 'error';
            $result['msg'] = '対応する字幕データがありません';
        }
        return $result;
    }

    /**
     * Youtubeの動画Urlであるか
     * @return bool
     */
    public function isMatchYoutubeUrl(): bool
    {
        return $this->isYoutubeUrl;
    }

    /**
     * 動画がすでに登録されているか
     * @param TypingGameModel $typingGameModel
     * @return bool
     */
    public function isAlreadyRegisteredUrl(TypingGameModel $typingGameModel): bool
    {
        return $typingGameModel->isExistRegisteredVideo($this->videoId);
    }

    /**
     * 指定された言語の字幕があるか
     * @param string $languageType 言語(Japanese, English, ...)
     * @param ScrappingTypeText $scrappingTypeText
     * @return bool
     */
    public function isExistLanguageScript(string $languageType, ScrappingTypeText $scrappingTypeText): bool
    {
        // videoIdから字幕情報タイピング情報取得.
        $languageList = $scrappingTypeText->getScriptLanguageList();
        $langListIndex = array_search($languageType, $languageList);
        $this->langListIndex = $langListIndex;
        return $langListIndex !== false;
    }
}