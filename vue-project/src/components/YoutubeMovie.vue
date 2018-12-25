<template>
    <div class="youtube-sec">
        <button @click="playVideo">play</button>
        <br>
        <youtube :videoId="videoId" ref="youtube" @playing="playing"></youtube>
        <br>
        <button @click="startType">start</button>
        <br>
        <span>{{displayText}}</span>
        <br>
        <span id="inputtedText">{{ inputtedText }}</span><span id="restText">{{ restText }}</span>
        <br>
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
      // すべてのテキスト.{startTime:..., endEtime:..., hurigana:..., text:...}
      allPhraseData: "",
      /** 表示するテキスト */
      displayText:"あっとおどろく",//"るどむーびーみたしねま",
      /** 入力するテキスト */
      inputText: "あっとおどろく",//"るどむーびーみたしねま",
      /** 入力したかな文字数 */
      inputtedKanaNum: 0,
      /** 入力したカナ文字 */
      inputtedText: "",
      /** 残りの入力するテキスト */
      restText: "あっとおどろく",//"るどむーびーみたしねま",
      /** 入力したローマ字(画面に表示する) */
      inputRoman: "",
      /** マッチしたチャンク */
      matchedChunk: "",
      /** ローマ字入力の候補(インデックスは入力するひらがな文字数). */
      romanChunkCandidate: [],
      /** キー入力禁止フラグ(キー入力を受け付けない) */
      finishFlg: false,
      /** 1フレーム入力完了 */
      phraseEndFlg:false,

      // typing中の変数
      /** タイマー */
      timer : 0,
      /** 現在再生している動画の再生時間 */
      movieTime:0,
      /** 次のフレーズNo */
      nextPhraseNo : 0,
    }
  },
  beforeMount() {
    window.addEventListener('keydown', this.onKeyDownEvent);
  },
  created() {
    console.log("created");
    // サーバからajaxでデータ取得.
    let url = "http://localhost:8081/getTypeText?videoId="+this.videoId;
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
    playVideo() {
      this.player.playVideo();
    },
    playing() {
      console.log("we are watching!!!");
      console.log(this.player.getCurrentTime());
    },
    // タイピング開始.
    startType(event) {
      // 動画再生.
      this.player.playVideo();
      this.startingType();

      console.log("start");
    },
    startingType(){
      let now = Date.now();
      if (now - this.timer > FRAME_TIME) {
        // ループ処理開始 //
        // 現在の再生時間取得.
        this.player.getCurrentTime().then((cTime)=>this.movieTime= cTime);
        // TODO: 後ろに巻き戻したら戻る.
        // TODO: 先に飛ばしたら先に行く.
        // テキスト表示時間になったら次のフレーズを表示する.
        if(this.allPhraseData.length > this.nextPhraseNo
          && this.movieTime > this.allPhraseData[this.nextPhraseNo]['startTime']){
            // 次のフレーズを表示する.
            this.displayText = this.allPhraseData[this.nextPhraseNo]['text'];
            /** 入力するテキスト */
            this.inputText = this.allPhraseData[this.nextPhraseNo]['Furigana'];
            /** 入力したかな文字数 */
            this.inputtedKanaNum = 0;
            /** 入力したカナ文字 */
            this.inputtedText = "";
            /** 残りの入力するテキスト */
            this.restText = this.inputText;
            /** 入力したローマ字(画面に表示する) */
            this.inputRoman = "";
            /** マッチしたチャンク */
            this.matchedChunk = "";
            /** ローマ字入力の候補(インデックスは入力するひらがな文字数). */
            this.romanChunkCandidate = [];
            this.phraseEndFlg = false;
            this.nextPhraseNo++;
        }
        // ループ処理終了 //
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
        this.romanChunkCandidate = this.searchNextRomanChunkCandidate(this.restText);
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
          // 入力文字が入力候補と合うか.
          if (candidate.indexOf(this.matchedChunk + event.key) === 0) {
            this.inputRoman += event.key;
            this.matchedChunk += event.key;
            console.log("チャンク部分マッチ");
            // 入力候補と完全一致したらそのチャンクは入力完了.
            console.log(candidate.length +"  "+ this.matchedChunk.length);
            if (candidate.length === this.matchedChunk.length) {
              this.matchedChunk = "";
              this.romanChunkCandidate = [];
              this.restText = this.restText.slice(hiraganaNum + 1);
              this.inputtedKanaNum += hiraganaNum + 1;
              this.inputtedText = this.inputText.slice(0, this.inputtedKanaNum);
              this.phraseEndFlg = this.restText.length === 0;
              matchFlg = true;
            }
          }
        });
      });
    },
    // テキストから次のローマ字入力候補を出力.
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
      if (!this.isHiragana(firstChar)) {
        console.log("半角");
        return [[firstChar]];
      }
      // 全角ひらがなは5パターンに分けてローマ字変換.
      // ひらがな１文字のみ.
      if (text.length === 1) {
        console.log('ひらがな1文字');
        return [singleRoman];
      }
      if (this.isOOmozi(firstChar)) {
        // んなの場合
        if (firstChar === "ん" && secondChar === "な"){
          return [["nn"]]
        }
        // 全角大文字＋全角大文字 は最初の文字をローマ字変換して返す.
        else if (this.isOOmozi(secondChar)) {
          console.log("ひらがな2文字以上");
          return [singleRoman];
        }
        // 大文字＋小文字
        else if (this.isKomozi(secondChar)) {
          console.log("大文字＋小文字");
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
        if (indexName == hiragana) {
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
  computed: {
    player() {
      return this.$refs.youtube.player;
    }
  }
};
</script>

<style scoped>
#restText {
}

#inputtedText {
  font-weight: bolder;
}
</style>
