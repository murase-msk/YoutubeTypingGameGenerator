<template>
    <div class="youtube-sec" ref="r">
        <!--<div id="blockPanel" style="position:absolute; left: 50%; transform: translateX(-50%);height:360px;width:640px; z-index:0;"></div>-->
        <youtube :videoId="videoId" :player-vars="playerVars" ref="youtube" @playing="playing" style="z-index:99;"></youtube>
        <br>
        <div>{{displayText}}</div>
        <div id="inputPhrase">
            <span id="inputtedPhrase">
                <span class="inputtedText" v-for="oneInputedText in inputtedText ">{{ oneInputedText+" " }}</span>
            </span>
            <span id="restPhrase">
               <span class="restText" v-for="oneRestText in restText" >{{ oneRestText+" " }}</span>
            </span>
        </div>
        <span>{{ inputRoman }}</span>

        <br>
        <span></span>
    </div>
</template>

<script>
import HiraganToRoman from "./HiraganaToRoman.vue";
const FRAME_TIME    = 16; // [ms/frame] 1フレームで約16ms
export default {
  name: "YoutubeMovie",
  props: {
    videoId: String
  },
  data() {
    return {
      // vue-youtubeの設定(https://developers.google.com/youtube/player_parameters?hl=ja)
      playerVars:{
        controls: 0,//プレーヤーコントロールは表示しない.
        disablekb: 1,// プレイヤーのキーボード操作禁止.
        fs: 0,  // 全画面ボタン表示しない.
        modestbranding: 1,//コントロールバーにYoutubeロゴを表示しない.
        showinfo: 0,//ユーザ情報を表示しない.
      },
      // すべてのテキスト.{startTime:..., endEtime:..., hurigana:..., text:...}
      allPhraseData: "",
      /** 表示するテキスト */
      displayText:"",//"るどむーびーみたしねま",
      /** 入力するテキスト (スペースで区切った配列)*/
      inputText: "",//["aa", "aa"],//"るどむーびーみたしねま",
      /** 入力したかな文字数 */
      inputtedKanaNum: 0,
      /** 入力したカナ文字 */
      inputtedText: "",
      /** 残りの入力するテキスト (スペースで区切った配列)　*/
      restText: [],//["aa", "aa"],//"るどむーびーみたしねま",
      /** 入力したローマ字(画面に表示する) */
      inputRoman: "",
      /** マッチしたチャンク */
      matchedChunk: "",
      /** ローマ字入力の候補(インデックスは入力するひらがな文字数). */
      romanChunkCandidate: [],
      /** キー入力禁止フラグ(キー入力を受け付けない) */
      finishFlg: false,
      /** 1フレーズ入力完了 */
      //phraseEndFlg:false,
      /** パート */
      textPart : 0,

      // typing中の変数
      /** タイマー */
      timer : 0,
      /** 現在再生している動画の再生時間 */
      movieTime:0,
      /** 次のフレーズNo*/
      nextPhraseNo : 0,
    }
  },
  computed: {
    player() {
      return this.$refs.youtube.player;
    }
  },
  beforeMount() {
    window.addEventListener('keydown', this.onKeyDownEvent);
  },
  created() {
    console.log("created");
    // サーバからajaxでデータ取得.
      // 8083はvue dev-serverのポート.
      // 8081はapacheのGuest80番をHostにフォワーディングした先のポート.
      // 開発環境(vue dev-server)の場合 port8083なのでajaxはport8081にする.
      let port = location.port === "8083" ? "8081" : location.port;
      let url = location.protocol+"//"+location.hostname+":"+port+"/getTypeText?videoId="+this.videoId;
    fetch(url).then(function (response) {
      return response.json();
    }).then(function (json) {
      this.allPhraseData = json;
      console.log(json);
    }.bind(this));
  },
  beforeDestroy() {
    window.removeEventListener("keydown", this.onKeyDownEvent);
  },
  methods: {
    // 再生ボタンを押したときの実行される(vue-youtube).
    playing() {
        // フォーカス外す
        document.activeElement.blur();
        if(this.movieTime === 0) {
        this.startingType();
      }
    },
    // タイピング開始.
    startType(event) {
      // 動画再生.
      if(this.movieTime === 0) {
         this.player.playVideo();
         this.startingType();
      }
    },
    // //
    // deleteEmptyText(textArr) {
    //   let item = [];
    //   for(let i=0; i<textArr.length; i++){
    //     if(textArr[i] !== ""){
    //       item.push(textArr[i]);
    //     }
    //   }
    //   return item;
    // },
    startingType(){
      let now = Date.now();
      if (now - this.timer > FRAME_TIME) {
        // ループ処理ここから //
        // 現在の再生時間取得.
        this.player.getCurrentTime().then((cTime)=>this.movieTime= cTime);
        // TODO: 後ろに巻き戻したら戻る.
        // TODO: 先に飛ばしたら先に行く.
        // テキスト表示時間になったら次のフレーズを表示する.
        if(this.allPhraseData.length > this.nextPhraseNo
          && this.movieTime > this.allPhraseData[this.nextPhraseNo]['startTime']){
            console.log('next phrase');
            // 次のフレーズが来たときの初期化処理.
            // 次のフレーズを表示する.
            this.displayText = this.allPhraseData[this.nextPhraseNo]['text'];
            /** 入力するテキスト */
            this.inputText = this.allPhraseData[this.nextPhraseNo]['Furigana'].split(' ');
            /** 入力したかな文字数 */
            this.inputtedKanaNum = 0;
            /** 入力したカナ文字 */
            this.inputtedText = [];
            /** 残りの入力するテキスト */
            this.restText = this.inputText.concat();// 値渡し.
            /** 入力したローマ字(画面に表示する) */
            this.inputRoman = "";
            /** マッチしたチャンク  チャンク・・・ローマ字入力された文字の内、ひらがなに変換される塊 */
            this.matchedChunk = "";
            /** ローマ字入力の候補(インデックスは入力するひらがな文字数). */
            this.romanChunkCandidate = [];
            //.
            this.textPart = 0;
            //this.phraseEndFlg = false;
            this.nextPhraseNo++;
        }
        // ループ処理ここまで //
        this.timer = now;
      }
      let requestId = requestAnimationFrame(this.startingType);
      // cancelAnimationFrame(requestId);
    },
    // キー入力がトリガーとなる.
    onKeyDownEvent(event) {
      // それぞれのひらがな入力に対するローマ字入力候補
      // 入力された文字が入力文字候補に当てはまるか.
      if (this.finishFlg) return;
      if (this.romanChunkCandidate.length === 0) {
        this.romanChunkCandidate = this.searchNextRomanChunkCandidate(this.restText[this.textPart]);
      }
      // マッチング中.
      // マッチング始め.
      // 提示されたテキストと等しいときだけ表示する.
      // hiraganaNum チャンクに対するひらがなの文字数.
      console.log("候補" + this.romanChunkCandidate);
      this.romanChunkCandidate.forEach((candidateArray, hiraganaNum, array) => {
        if (candidateArray == -1) {
          return;
        }
        let matchFlg = false;
        candidateArray.forEach((candidate, index, array2) => {
          if (matchFlg === true) return;//
          if (candidate.indexOf(this.matchedChunk + event.key) === 0) {
            this.inputRoman += event.key;
            this.matchedChunk += event.key;
            // console.log("チャンク部分マッチ");
            // 入力候補と完全一致したらそのチャンクは入力完了.
            // console.log(candidate.length +"  "+ this.matchedChunk.length);
            if (candidate.length === this.matchedChunk.length) {
              this.matchedChunk = "";
              this.romanChunkCandidate = [];
              // 残りの入力テキストを減らす.
              this.restText[this.textPart] = this.restText[this.textPart].slice(hiraganaNum + 1);
              // 入力済みテキストを追加.
              this.inputtedKanaNum += hiraganaNum + 1;
              this.inputtedText.length <= this.textPart ? this.inputtedText.push("") : null ;
              this.inputtedText[this.textPart] = this.inputText[this.textPart].slice(0, this.inputtedKanaNum);
              //this.phraseEndFlg = this.restText[this.textPart].length === 0;
              // 次のテキストパートに行くか.
              if(this.restText[this.textPart].length === 0){
//                this.inputtedText[this.textPart] += " ";
                this.inputRoman += " ";
                this.textPart++;
                this.inputtedKanaNum = 0;
              }
              matchFlg = true;
            }
          }
        });
      });
    },
    // テキストから次のローマ字入力候補を出力.
    // インデックスは文字数.
    searchNextRomanChunkCandidate(text) {
      // 残りの入力テキストのうち最初の文字.
      let firstChar = text.slice(0, 1);
      // 残りの入力テキストのうち2番目文字.
      let secondChar = text.slice(1, 2);
      // 残りの入力テキストのうち3番目文字.
      let thirdChar = text.slice(2, 3);
      // 残りの入力テキストのうち最初の文字をローマ字返還した時の候補の配列
      let singleRoman = this.convertHiraganaToRoman(firstChar);
      // 残りの入力テキストのうち2文字目までをローマ字返還した時の候補の配列
      let doubleRoman = this.convertHiraganaToRoman(firstChar + secondChar);
      // 残りの入力テキストのうち3文字目までをローマ字返還した時の候補の配列
      let tripleRoman = this.convertHiraganaToRoman(firstChar + secondChar + thirdChar);
      // 半角はそのまま入力.
      if (this.isHankakuEisuuzi(firstChar)) {
        // console.log("半角");
        return [[firstChar]];
      }
      // 全角ひらがなは5パターンに分けてローマ字変換.
      // ひらがな１文字のみ.
      if (text.length === 1) {
        // console.log('ひらがな1文字');
        return [singleRoman];
      }
      if (this.isOOmozi(firstChar)) {
        // 「んな」の場合
        if (firstChar === "ん" && secondChar === "な"){
          return [["nn"]]
        }
        // 全角大文字＋全角大文字 は最初の文字をローマ字変換して返す.
        else if (this.isOOmozi(secondChar)) {
          // console.log("ひらがな2文字以上");
          return [singleRoman];
        }
        // 大文字＋小文字
        else if (this.isKomozi(secondChar)) {
          // console.log("大文字＋小文字");
          return [singleRoman, doubleRoman];
        }
        // それ以外
        console.log("大文字＋それ以外");
      } else if (this.isKomozi(firstChar)) {
        // 小文字+大文字＋小文字
        if (text.length >= 3 && this.isOOmozi(secondChar) && this.isKomozi(thirdChar)) {
          return [singleRoman, doubleRoman, tripleRoman];
        }
        // 小文字+大文字
        else if (this.isOOmozi(secondChar)) {
          return [singleRoman, doubleRoman];
        }
        console.log("小文字+それ以外");
      }
      console.log("例外発生");
    },
    // 半角英数字であるか.
    isHankakuEisuuzi(input){
      return input.match(/^[a-z0-9]/) ? true : false;
    },
    // 全角のひらがなかどうか.
    isHiragana(input) {
      return input.match(/^[\u3041-\u3096|\u30FC]/) ? true : false;
    },
    // ひらがな且つ、小文字かどうか.
    // ぁ3041, ぃ3043, ぅ3045, ぇ3047, ぉ3049, っ3063, ゃ3083, ゅu3085,ょ 3087, ゎ308E
    isKomozi(input) {
      return this.isHiragana(input) && input.match(/^[\u3041\u3043\u3045\u3047\u3049\u3063\u3083\u3085\u3087\u308E]+$/) ? true : false;
    },
    // ひらがな大文字か(ひらがな且つ、小文字でない).
    isOOmozi(input) {
      return this.isHiragana(input) && !this.isKomozi(input);
    },
    // ひらがなをローマ字に変換.
    convertHiraganaToRoman(hiragana) {
      // TODO 直前に「ん」があれば「n」を重ねても良い.
      // 先頭文字が「っ」(促音)であれば直後の１文字を重ねる
      // 「っ」(促音)であるときフラグを立てる.
      let sokuonFlg = false;
      if (hiragana.slice(0, 1) === "っ") {
        sokuonFlg = true;
        hiragana = hiragana.length === 1 ? hiragana : hiragana.slice(1);
      }
      // ひらがなをローマ字に変換する変換表.
      const roman = HiraganToRoman.data().roman;
      for (const indexName in roman) {
        if (indexName === hiragana) {
          if (sokuonFlg && hiragana !== "っ") {
            // 先頭に「っ」(促音)がありかつ「っ」のみでなければ直前の文字を重ねる.
            return roman[indexName].map(char => char.slice(0, 1) + char);
          } else {
            return roman[indexName];
          }
        }
      }
      return -1;
    }
  },
};
</script>

<style scoped>
.inputtedText {
  font-weight: bolder;
}
#topInputtedText > span.inputtedText:not(:first-child),
#topRestText > span.restText:not(:first-child){
    /*margin-left: 5px;*/
}
</style>
