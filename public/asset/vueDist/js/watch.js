(function(t){function e(e){for(var i,r,u=e[0],s=e[1],h=e[2],c=0,y=[];c<u.length;c++)r=u[c],a[r]&&y.push(a[r][0]),a[r]=0;for(i in s)Object.prototype.hasOwnProperty.call(s,i)&&(t[i]=s[i]);l&&l(e);while(y.length)y.shift()();return o.push.apply(o,h||[]),n()}function n(){for(var t,e=0;e<o.length;e++){for(var n=o[e],i=!0,u=1;u<n.length;u++){var s=n[u];0!==a[s]&&(i=!1)}i&&(o.splice(e--,1),t=r(r.s=n[0]))}return t}var i={},a={watch:0},o=[];function r(e){if(i[e])return i[e].exports;var n=i[e]={i:e,l:!1,exports:{}};return t[e].call(n.exports,n,n.exports,r),n.l=!0,n.exports}r.m=t,r.c=i,r.d=function(t,e,n){r.o(t,e)||Object.defineProperty(t,e,{enumerable:!0,get:n})},r.r=function(t){"undefined"!==typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},r.t=function(t,e){if(1&e&&(t=r(t)),8&e)return t;if(4&e&&"object"===typeof t&&t&&t.__esModule)return t;var n=Object.create(null);if(r.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var i in t)r.d(n,i,function(e){return t[e]}.bind(null,i));return n},r.n=function(t){var e=t&&t.__esModule?function(){return t["default"]}:function(){return t};return r.d(e,"a",e),e},r.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},r.p="/";var u=window["webpackJsonp"]=window["webpackJsonp"]||[],s=u.push.bind(u);u.push=e,u=u.slice();for(var h=0;h<u.length;h++)e(u[h]);var l=s;o.push([0,"chunk-vendors"]),n()})({0:function(t,e,n){t.exports=n("56d7")},"034f":function(t,e,n){"use strict";var i=n("c1ff"),a=n.n(i);a.a},"4e0d":function(t,e,n){"use strict";var i=n("af6d"),a=n.n(i);a.a},"56d7":function(t,e,n){"use strict";n.r(e);n("94d6"),n("8915"),n("8a38");var i,a,o=n("8852"),r=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{attrs:{id:"app"}},[n("YoutubeMovie",{attrs:{videoId:t.videoId}})],1)},u=[],s=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{ref:"r",staticClass:"youtube-sec"},[n("youtube",{ref:"youtube",staticStyle:{"z-index":"99"},attrs:{videoId:t.videoId,"player-vars":t.playerVars},on:{playing:t.playing}}),n("br"),n("button",{staticClass:"btn btn-secondary",on:{click:t.startType}},[t._v("start")]),n("br"),n("span",[t._v(t._s(t.displayText))]),n("br"),t._l(t.inputtedText,function(e){return n("span",{staticClass:"inputtedText"},[t._v(t._s(e+" "))])}),t._l(t.restText,function(e){return n("span",{staticClass:"restText"},[t._v(t._s(e+" "))])}),n("br"),n("span",[t._v(t._s(t.inputRoman))]),n("br"),n("span")],2)},h=[],l=(n("46c2"),n("44ba"),n("1fe3"),{name:"HiraganaToRoman",data:function(){return{roman:{"あ":["a"],"い":["i","yi"],"う":["u","wu"],"え":["e"],"お":["o"],"か":["ka"],"き":["ki"],"く":["ku"],"け":["ke"],"こ":["ko"],"さ":["sa"],"し":["si"],"す":["su"],"せ":["se"],"そ":["so"],"た":["ta"],"ち":["ti","chi"],"つ":["tu","tsu"],"て":["te"],"と":["to"],"な":["na"],"に":["ni"],"ぬ":["nu"],"ね":["ne"],"の":["no"],"は":["ha"],"ひ":["hi"],"ふ":["hu","fu"],"へ":["he"],"ほ":["ho"],"ま":["ma"],"み":["mi"],"む":["mu"],"め":["me"],"も":["mo"],"や":["ya"],"ゆ":["yu"],"いぇ":["ye"],"よ":["yo"],"ら":["ra"],"り":["ri"],"る":["ru"],"れ":["re"],"ろ":["ro"],"わ":["wa"],"ゐ":["wyi"],"ゑ":["wye"],"を":["wo"],"ん":["nn","n"],"が":["ga"],"ぎ":["gi"],"ぐ":["gu"],"げ":["ge"],"ご":["go"],"ざ":["za"],"じ":["zi","ji"],"ず":["zu"],"ぜ":["ze"],"ぞ":["zo"],"だ":["da"],"ぢ":["di"],"づ":["du"],"で":["de"],"ど":["do"],"ば":["ba"],"び":["bi"],"ぶ":["bu"],"べ":["be"],"ぼ":["bo"],"ぱ":["pa"],"ぴ":["pi"],"ぷ":["pu"],"ぺ":["pe"],"ぽ":["po"],"きゃ":["kya"],"きゅ":["kyu"],"きょ":["kyo"],"しゃ":["sya"],"しゅ":["syu"],"しょ":["syo"],"ちゃ":["tya","cha"],"ちぃ":["tyi"],"ちゅ":["tyu","chu"],"ちぇ":["tye","che"],"ちょ":["tyo","cho"],"にゃ":["nya"],"にぃ":["nyi"],"にゅ":["nyu"],"にぇ":["nye"],"にょ":["nyo"],"ひゃ":["hya"],"ひぃ":["hyi"],"ひゅ":["hyu"],"ひぇ":["hye"],"ひょ":["hyo"],"みゃ":["mya"],"みぃ":["myi"],"みゅ":["myu"],"みぇ":["mye"],"みょ":["myo"],"りゃ":["rya"],"りぃ":["ryi"],"りゅ":["ryu"],"りぇ":["rye"],"りょ":["ryo"],"ぎゃ":["gya"],"ぎぃ":["gyi"],"ぎゅ":["gyu"],"ぎぇ":["gye"],"ぎょ":["gyo"],"じゃ":["jya","ja","zya"],"じぃ":["jyi","zyi"],"じゅ":["jyu","ju","zyu"],"じぇ":["jye","je","zye"],"じょ":["jyo","jo","zyo"],"ぢゃ":["dya"],"ぢぃ":["dyi"],"ぢゅ":["dyu"],"ぢぇ":["dye"],"ぢょ":["dyo"],"びゃ":["bya"],"びぃ":["byi"],"びゅ":["byu"],"びぇ":["bye"],"びょ":["byo"],"ぴゃ":["pya"],"ぴぃ":["pyi"],"ぴゅ":["pyu"],"ぴぇ":["pye"],"ぴょ":["pyo"],"ふぁ":["fa"],"ふぃ":["fi"],"ふぇ":["fe"],"ふぉ":["fo"],"ふゃ":["fya"],"ふゅ":["fyu"],"ふょ":["fyo"],"ぁ":["xa","la"],"ぃ":["xi","li"],"ぅ":["xu","lu"],"ぇ":["xe","le"],"ぉ":["xo","lo"],"ゃ":["xya","lya"],"ゅ":["xyu","lyu"],"ょ":["xyo","lyo"],"っ":["xtu","ltu","xtsu"],"うぃ":["wi"],"うぇ":["we"],"ヴぁ":["va"],"ヴぃ":["vi"],"ヴ":["vu"],"ヴぇ":["ve"],"ヴぉ":["vo"],"ー":["-"]}}}}),c=l,y=n("b0c5"),d=Object(y["a"])(c,i,a,!1,null,null,null);d.options.__file="HiraganaToRoman.vue";var f=d.exports,p=16,m={name:"YoutubeMovie",props:{videoId:String},data:function(){return{playerVars:{controls:0,disablekb:1,fs:0,modestbranding:1,showinfo:0},allPhraseData:"",displayText:"あっと おどろく",inputText:"",inputtedKanaNum:0,inputtedText:"",restText:["aa","aa"],inputRoman:"",matchedChunk:"",romanChunkCandidate:[],finishFlg:!1,textPart:0,timer:0,movieTime:0,nextPhraseNo:0}},computed:{player:function(){return this.$refs.youtube.player}},beforeMount:function(){window.addEventListener("keydown",this.onKeyDownEvent)},created:function(){console.log("created");var t="http://localhost:8081/getTypeText?videoId="+this.videoId;fetch(t).then(function(t){return t.json()}).then(function(t){this.allPhraseData=t,console.log(t)}.bind(this))},beforeDestroy:function(){window.removeEventListener("keydown",this.onKeyDownEvent)},methods:{playing:function(){0===this.movieTime&&this.startingType(),console.log(document.getElementsByTagName("body")[0]),this.$nextTick(function(){return document.getElementsByTagName("body")[0].focus()})},startType:function(t){0===this.movieTime&&(this.player.playVideo(),this.startingType())},startingType:function(){var t=this,e=Date.now();e-this.timer>p&&(this.player.getCurrentTime().then(function(e){return t.movieTime=e}),this.allPhraseData.length>this.nextPhraseNo&&this.movieTime>this.allPhraseData[this.nextPhraseNo]["startTime"]&&(console.log("next phrase"),this.displayText=this.allPhraseData[this.nextPhraseNo]["text"],this.inputText=this.allPhraseData[this.nextPhraseNo]["Furigana"].split(" "),this.inputtedKanaNum=0,this.inputtedText=[],this.restText=this.inputText.concat(),this.inputRoman="",this.matchedChunk="",this.romanChunkCandidate=[],this.textPart=0,this.nextPhraseNo++),this.timer=e);requestAnimationFrame(this.startingType)},onKeyDownEvent:function(t){var e=this;this.finishFlg||(0===this.romanChunkCandidate.length&&(this.romanChunkCandidate=this.searchNextRomanChunkCandidate(this.restText[this.textPart])),console.log("候補"+this.romanChunkCandidate),this.romanChunkCandidate.forEach(function(n,i,a){if(-1!=n){var o=!1;n.forEach(function(n,a,r){!0!==o&&0===n.indexOf(e.matchedChunk+t.key)&&(e.inputRoman+=t.key,e.matchedChunk+=t.key,n.length===e.matchedChunk.length&&(e.matchedChunk="",e.romanChunkCandidate=[],e.restText[e.textPart]=e.restText[e.textPart].slice(i+1),e.inputtedKanaNum+=i+1,e.inputtedText.length<=e.textPart&&e.inputtedText.push(""),e.inputtedText[e.textPart]=e.inputText[e.textPart].slice(0,e.inputtedKanaNum),0===e.restText[e.textPart].length&&(e.inputRoman+=" ",e.textPart++,e.inputtedKanaNum=0),o=!0))})}}))},searchNextRomanChunkCandidate:function(t){var e=t.slice(0,1),n=t.slice(1,2),i=t.slice(2,3),a=this.convertHiraganaToRoman(e),o=this.convertHiraganaToRoman(e+n),r=this.convertHiraganaToRoman(e+n+i);if(this.isHankakuEisuuzi(e))return[[e]];if(1===t.length)return[a];if(this.isOOmozi(e)){if("ん"===e&&"な"===n)return[["nn"]];if(this.isOOmozi(n))return[a];if(this.isKomozi(n))return[a,o];console.log("大文字＋それ以外")}else if(this.isKomozi(e)){if(t.length>=3&&this.isOOmozi(n)&&this.isKomozi(i))return[a,o,r];if(this.isOOmozi(n))return[a,o];console.log("小文字+それ以外")}console.log("例外発生")},isHankakuEisuuzi:function(t){return!!t.match(/^[a-z0-9]/)},isHiragana:function(t){return!!t.match(/^[\u3041-\u3096|\u30FC]/)},isKomozi:function(t){return!(!this.isHiragana(t)||!t.match(/^[\u3041\u3043\u3045\u3047\u3049\u3063\u3083\u3085\u3087\u308E]+$/))},isOOmozi:function(t){return this.isHiragana(t)&&!this.isKomozi(t)},convertHiraganaToRoman:function(t){var e=!1;"っ"===t.slice(0,1)&&(e=!0,t=1===t.length?t:t.slice(1));var n=f.data().roman;for(var i in n)if(i===t)return e&&"っ"!==t?n[i].map(function(t){return t.slice(0,1)+t}):n[i];return-1}}},v=m,x=(n("4e0d"),Object(y["a"])(v,s,h,!1,null,"0e9eaaf8",null));x.options.__file="YoutubeMovie.vue";var g=x.exports,b=n("2de7"),T=n.n(b);o["a"].use(T.a);var k={name:"app",components:{YoutubeMovie:g},data:function(){return{videoId:document.getElementsByName("videoId")[0].value}}},w=k,P=(n("034f"),Object(y["a"])(w,r,u,!1,null,null,null));P.options.__file="App.vue";var _=P.exports;o["a"].config.productionTip=!1,new o["a"]({render:function(t){return t(_)}}).$mount("#app")},af6d:function(t,e,n){},c1ff:function(t,e,n){}});
//# sourceMappingURL=watch.js.map