<template>
    <div id="editScript">
        <youtube :videoId="videoId" :player-vars="playerVars" ref="youtube" @playing="playing" style="z-index:99;"></youtube>
        <br>
        <!--実際に送る用-->
        <form action="/content1/saveContent" method="post" name="saveTypeInfo">
            <input type="hidden" name="csrf_name" :value=csrf.csrf_name>
            <input type="hidden" name="csrf_value" :value=csrf.csrf_value>
            <input type="hidden" name="videoId" :value=videoId>
            <input type="hidden" name="typeInfo" :value=JSON.stringify(allPhraseData)>
            <input type="submit" value="保存" class="btn btn-secondary">
        </form>

        <form action="/content1/saveContent" method="post" name="editPhrase">
            <input type="hidden" name="videoId" :value="videoId">
            <div style="width:100%;height:200px;overflow:auto;">
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
                        <tr v-for="phraseData in allPhraseData" :id="'phrase'+phraseData.index">
                            <td class="no">
                                {{ phraseData.index}}
                            </td>
                            <td class="start">
                                <input v-model="phraseData.startTime" type="text" name="startTime" size="3">
                            </td>
                            <td class="end">
                                <input v-model="phraseData.endTime"  type="text" name="endTime" size="3">
                            </td>
                            <td class="displayText">
                                <input v-model="phraseData.text"  type="text" name="typeText" size="40">
                            </td>
                            <td class="inputText">
                                <input v-model="phraseData.Furigana"  type="text" name="typeText" size="40">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</template>

<script>
    const FRAME_TIME    = 16; // [ms/frame] 1フレームで約16ms
    export default {
        name: "EditScript",
        props: {
            videoId: String
        },
        data() {
            return {
                // vue-youtubeの設定
                playerVars:{
                    fs: 0,
                    modestbranding: 1,
                    showinfo: 0,
                },
                // すべてのテキスト.{startTime:..., endEtime:..., hurigana:..., text:...}
                allPhraseData: "",
                // 再生時間.
                movieTime:0,
                /** タイマー */
                timer : 0,

                csrf: {
                    csrf_name:document.getElementsByName('csrf_name')[0].value,
                    csrf_value:document.getElementsByName('csrf_value')[0].value
                }
            }
        },
        computed: {
            player() {
                console.log(this.$refs.youtube);
                return this.$refs.youtube.player;
            }
        },
        created() {
            // サーバからajaxでデータ取得.
            // 8083はvue dev-serverのポート.
            // 8081はapacheのGuest80番をHostにフォワーディングした先のポート.
            // 開発環境(vue dev-server)の場合 port8083なのでajaxはport8081にする.
            let port = location.port === 8083 ? 8081 : location.port;
            let url = location.protocol + "//" + location.hostname +":"+port+ "/getTypeText?videoId=" + this.videoId;
            fetch(url).then(function (response) {
                return response.json();
            }).then(function (json) {
                this.allPhraseData = json;
                console.log(json);
                this.checkPhraseNo();
            }.bind(this));

        },
        methods:{
            // 再生ボタンを押したときの実行される(vue-youtube).
            playing() {
                console.log("playing");
            },
            // 再生している時間はどのフレーズか.
            checkPhraseNo(){
                let now = Date.now();
                 if (now - this.timer > FRAME_TIME*30) {
                    // ループ処理ここから //
                    // 現在の再生時間取得.
                     this.player.getCurrentTime().then((cTime)=>this.movieTime= cTime);
                    let phraseNo = -1;// 現在の再生時間は何番目のフレーズであるか.
                    // テキスト表示時間になったら指定のフレーズを表示する.
                    this.allPhraseData.forEach((data, index, arr)=>{
                        if(data.startTime < this.movieTime && this.movieTime < data.endTime){
                            phraseNo = index;
                        }
                    });
                    if(phraseNo !== -1) {
                        document.getElementById("phrase" + phraseNo).scrollIntoView(true);
                    }
                    // ループ処理ここまで //
                    this.timer = now;
                 }
                 let requestId = requestAnimationFrame(this.checkPhraseNo);
            },
        }
    }
</script>

<style scoped>
    /*スクロール用*/
    thead.table_head,tbody.table_scroll{
        display:block;
    }
    tbody.table_scroll{
        overflow-y:scroll;
        height:100px;
    }

    /*幅調整*/
    td,th{
        table-layout:fixed;
    }
    .no{
        width:30px;
    }
    .start, .end{
        width:54px;
    }
    .displayText, .inputText{
        width: 315px;
    }
</style>