<template>
    <div>
        <div v-if="isAuth">
            <div v-if="isBookmark">
                <img id="bookmark" src="../assets/bookmark16px.jpeg" @click="changeBookmark">ブックマーク済み
            </div>
            <div v-else>
                <img id="unbookmark" src="../assets/unbookmark16px.jpeg" @click="changeBookmark">ブックマークする
            </div>
        </div>
        <div v-else>
            ログインするとブックマーク機能が使えます
        </div>
    </div>
</template>

<script>
    export default {
        name: "Bookmark",
        props: {
            // VideoId.
            videoId: String,
            // ログインしているか.
            isAuth: Boolean,
            csrf_name: String,
            csrf_value: String,
        },
        data(){
            return {
                port:String,
                // ブックマークしているか.
                isBookmark: Boolean,
            }
        },
        created() {
            // サーバからajaxでデータ取得.
            // 8083はvue dev-serverのポート.
            // 8081はapacheのGuest80番をHostにフォワーディングした先のポート.
            // 開発環境(vue dev-server)の場合 port8083なのでajaxはport8081にする.
            this.port = location.port === "8083" ? "8081" : location.port;

            const obj = {"videoId":this.videoId, "csrf_name":this.csrf_name, "csrf_value":this.csrf_value};
            const method = "POST";
            //const body = Object.keys(obj).reduce((o,key)=>(o.set(key, obj[key])), new FormData());
            const body = JSON.stringify(obj);
            const headers = {"Content-type" : "application/json"};
            // post (multipart/form-data)
            let url = location.protocol+"//"+location.hostname+":"+this.port+"/bookmark/isBookmark";
            fetch(url,{method, headers, body}).then(function (response) {
                return response.json();
            }).then(function (json) {
                if(json.noError === true) {
                    this.isBookmark = json.isBookmark;
                }

            }.bind(this));
        },
        methods:{
            // ブックマーク登録・削除処理.
            changeBookmark: function(event){
                console.log("bookmark");
                // TODO:ajaxで処理する
                const obj = {"videoId":this.videoId, "isBookmark":this.isBookmark, "csrf_name":this.csrf_name, "csrf_value":this.csrf_value};
                const method = "POST";
                //const body = Object.keys(obj).reduce((o,key)=>(o.set(key, obj[key])), new FormData());
                const body = JSON.stringify(obj);
                const headers = {"Content-type" : "application/json"};
                // post (multipart/form-data)
                const url = location.protocol+"//"+location.hostname+":"+this.port+"/bookmark/changeBookmark";
                fetch(url,{method, headers, body}).then(function (response) {
                    return response.json();
                }).then(function (json) {
                    if(json.noError === true) {
                        this.isBookmark = json.isBookmark;
                    }
               }.bind(this));
            },
        }
    }
</script>

<style scoped>
    #bookmark, #unbookmark {
        cursor : pointer;
    }
</style>