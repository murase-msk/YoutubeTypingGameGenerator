<template>
  <div id="editScript">
    <youtube
      :videoId="videoId"
      :player-vars="playerVars"
      ref="youtube"
      @playing="playing"
      style="z-index:99;"
    ></youtube>
    <br />
    <!--実際に送る用-->
    <form action="/typingGame/saveContent" method="post" name="saveTypeInfo">
      <input type="hidden" name="csrf_name" :value="csrf.csrf_name" />
      <input type="hidden" name="csrf_value" :value="csrf.csrf_value" />
      <input type="hidden" name="videoId" :value="videoId" />
      <input
        type="hidden"
        name="typeInfo"
        :value="JSON.stringify(allPhraseData)"
      />
      <input type="submit" value="保存" class="btn btn-secondary" />
    </form>
    <form action="/typingGame/saveContent" method="post" name="editPhrase">
      <input type="hidden" name="videoId" :value="videoId" />
      <div id="input_table">
        <table align="center">
          <thead class="table_head">
            <tr>
              <th class="no"></th>
              <th class="start">start</th>
              <th class="end">end</th>
              <th class="displayText">表示テキスト</th>
              <th class="inputText">入力文字</th>
            </tr>
          </thead>
          <tbody class="table_scroll">
            <!-- 再生位置を流しているときにclass=acticeになる -->
            <tr
              v-for="(phraseData, index) in allPhraseData"
              v-bind:key="index"
              v-bind:id="'phrase' + phraseData.index"
              v-bind:class="[
                phraseData.index === phraseNo ? 'active' : 'non-active'
              ]"
            >
              <td class="no">{{ phraseData.index }}</td>
              <td
                class="start"
                v-bind:class="[
                  phraseData.index == activeFieldIndex / 2.0
                    ? 'active'
                    : 'non-active'
                ]"
              >
                <input
                  v-model="phraseData.startTime"
                  type="text"
                  name="startTime"
                  size="3"
                  @focus="setForcesForRecordMode"
                />
              </td>
              <td
                class="end"
                v-bind:class="[
                  phraseData.index + 0.5 == activeFieldIndex / 2.0
                    ? 'active'
                    : 'non-active'
                ]"
              >
                <input
                  v-model="phraseData.endTime"
                  type="text"
                  name="endTime"
                  size="3"
                  @focus="setForcesForRecordMode"
                />
              </td>
              <td class="displayText">
                <input
                  v-model="phraseData.text"
                  type="text"
                  name="typeText"
                  size="40"
                />
              </td>
              <td class="inputText">
                <input
                  v-model="phraseData.Furigana"
                  type="text"
                  name="typeText"
                  size="40"
                />
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </form>
    <!-- 再生時間に応じてそのタイミングの列を表示する -->
    <label
      ><input
        type="radio"
        name="mode"
        value="edit"
        v-model="mode"
      />編集モード</label
    >
    <!-- 記録しようとしている列を一番から3番目くらいにする -->
    <label
      ><input
        type="radio"
        name="mode"
        value="record"
        v-model="mode"
      />記録モード</label
    >
    <button v-on:click="recordAndMoveNext" name="record-time">
      タイミングを記録
    </button>
    <button name="delete-line">選択行を削除</button>
    <button name="add-line">選択行の下に追加</button>
  </div>
</template>

<script>
const FRAME_TIME = 16; // [ms/frame] 1フレームで約16ms
export default {
  name: "EditScript",
  props: {
    videoId: String
  },
  data() {
    //.
    return {
      // vue-youtubeの設定
      playerVars: {
        fs: 0,
        modestbranding: 1,
        showinfo: 0
      },
      // すべてのテキスト.[{startTime:..., endEtime:..., hurigana:..., text:...}, {...}...]
      allPhraseData: "",
      // 再生時間.
      movieTime: 0,
      /** タイマー */
      timer: 0,
      /** 編集モード(edit)or 記録モード(record) */
      mode: "edit",
      /** 現在の再生時間は何番目のフレーズであるか(mode=editで使用). (-1のときは未使用)*/
      phraseNo: -1,
      /** テキストボックス (値が5のときは5/2=2.5=3列目end)(mode=recordで使用) */
      activeFieldIndex: -1,
      /** csrf */
      csrf: {
        csrf_name: document.getElementsByName("csrf_name")[0].value,
        csrf_value: document.getElementsByName("csrf_value")[0].value
      }
    };
  },
  watch: {
    // ウォッチャ.
    mode: function(newMode, oldMode) {
      if (newMode === "edit") {
        this.activeFieldIndex = -1;
      } else if (newMode == "record") {
        this.phraseNo = -1;
        this.activeFieldIndex = 0;
      }
    }
  },
  computed: {
    // 算術プロパティ.
    player() {
      //console.log(this.$refs.youtube);
      return this.$refs.youtube.player;
    }
  },
  created() {
    //.
    // サーバからajaxでデータ取得.
    // 8083はvue dev-serverのポート.
    // 8081はapacheのGuest80番をHostにフォワーディングした先のポート.
    // 開発環境(vue dev-server)の場合 port8083なのでajaxはport8081にする.
    const port = location.port === "8083" ? "8081" : location.port;
    const url =
      location.protocol +
      "//" +
      location.hostname +
      ":" +
      port +
      "/typingGame/getTypeText?videoId=" +
      this.videoId;
    fetch(url)
      .then(response => {
        return response.json();
      })
      .then(json => {
        this.allPhraseData = json;
        this.checkPhraseNo();
      });
  },
  methods: {
    // メソッド.
    // 再生ボタンを押したときの実行される(vue-youtube).
    playing() {
      //console.log("playing");
    },
    // 再生している時間はどのフレーズか.
    checkPhraseNo() {
      const now = Date.now();
      if (now - this.timer > FRAME_TIME * 30) {
        // ループ処理ここから //
        // 現在の再生時間取得.
        this.player.getCurrentTime().then(cTime => (this.movieTime = cTime));
        if (this.mode === "edit") {
          // 編集モードのタイミング表示.
          // テキスト表示時間になったら指定のフレーズを表示する.
          this.allPhraseData.forEach((data, index, arr) => {
            if (
              data.startTime < this.movieTime &&
              this.movieTime < data.endTime
            ) {
              this.phraseNo = index;
            }
          });
          if (this.phraseNo !== -1) {
            // 表示の一番上に来るphraseNo(上から3番めが再生位置の表示になる.)
            let topPhraseNo = this.phraseNo > 2 ? this.phraseNo - 2 : 0;
            // スクロール.
            document
              .getElementById("phrase" + topPhraseNo)
              .scrollIntoView(true);
          }
        }
        // ループ処理ここまで //
        this.timer = now;
      }
      requestAnimationFrame(this.checkPhraseNo);
    },
    // 記録モードのフォーカス設定.
    setForcesForRecordMode() {
      if (this.mode === "record") {
        // フォーカスがあたっているテキストボックスを強調表示する.
        if (document.activeElement.getAttribute("name") === "startTime") {
          this.activeFieldIndex =
            parseInt(
              document.activeElement.parentNode.parentNode.firstElementChild
                .textContent
            ) * 2;
        } else if (document.activeElement.getAttribute("name") === "endTime") {
          this.activeFieldIndex =
            parseInt(
              document.activeElement.parentNode.parentNode.firstElementChild
                .textContent
            ) *
              2 +
            1;
        }
      }
    },
    // 記録モードでアクティブのテキストボックスに時間を記録して次のテキストボックスをアクティブにする.
    recordAndMoveNext() {
      if (this.mode !== "record") return;

      const index = parseInt(this.activeFieldIndex / 2);
      if (this.activeFieldIndex % 2.0 == 1) {
        // endのテキストボックス.
        this.allPhraseData[index].endTime =
          Math.round(this.movieTime * 1000) / 1000; // 小数点第３位で四捨五入.
      } else {
        //startのテキストボックス.
        this.allPhraseData[index].startTime =
          Math.round(this.movieTime * 1000) / 1000; // 小数点第３位で四捨五入.
      }
      // 次のテキストボックスをフォーカス.
      this.activeFieldIndex++;
      // スクロール.
    }
  }
};
</script>

<style scoped>
/*スクロール用*/
thead.table_head,
tbody.table_scroll {
  display: block;
}

tbody.table_scroll {
  overflow-y: scroll;
  height: 200px;
}

table#input_table {
  width: 100%;
  height: 250px;
  overflow: auto;
}

/** 強調表示 */
tr.active > td,
tr.active > td > input,
td.active > input {
  border: rgb(172, 122, 31);
  background-color:rgb(172, 122, 31);
}

/*幅調整*/
td,
th {
  table-layout: fixed;
}

.no {
  width: 30px;
}

.start,
.end {
  width: 54px;
}

.displayText,
.inputText {
  width: 315px;
}
</style>
