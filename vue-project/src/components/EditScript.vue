<template>
    <div id="editScript">
        <youtube :videoId="videoId" :player-vars="playerVars" ref="youtube" @playing="playing" style="z-index:99;"></youtube>
        <br>
        <button class="btn btn-secondary" @click="saveContent">保存</button>
        <form action="/edit/editPhrase" method="post" name="editPhrase">
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
                                <input type="text" name="startTime" :value="phraseData.startTime" size="3">
                            </td>
                            <td class="end">
                                <input type="text" name="endTime" :value="phraseData.endTime" size="3">
                            </td>
                            <td class="displayText">
                                <input type="text" name="typeText" :value="phraseData.text" size="40">
                            </td>
                            <td class="inputText">
                                <input type="text" name="typeText" :value="phraseData.Furigana" size="40">
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
            }
        },
        computed: {
            player() {
                return this.$refs.youtube.player;
            }
        },
        created() {
            // サーバからajaxでデータ取得.
            let url = "http://localhost:8081/getTypeText?videoId="+this.videoId;
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
            // TODO:編集した内容を保存する.
            saveContent(){
                document.editPhrase.submit();
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