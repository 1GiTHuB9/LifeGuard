//役割: ページのHTMLが完全に読み込まれた後に実行される処理を設定。
//チャットウィンドウの操作を行う前に、DOM（HTML構造）が読み込まれていることを保証します。
document.addEventListener("DOMContentLoaded", function () {

    //チャット画面のスクロール可能な領域（.chat-window）を取得。
    const chatWindow = document.querySelector(".chat-window");
    //ロード中フラグ。新しいデータの読み込み中に他のリクエストを送らないよう制御。
    let loading = false;
    let offset = 50; // 初回読み込み以降のデータ位置

    //役割: チャット画面でスクロールが発生したときに実行される処理を設定。
    chatWindow.addEventListener("scroll", function () {
        
        //スクロール位置が一番上の場合に新しいデータを取得する条件。
        if (chatWindow.scrollTop === 0 && !loading) {
            loading = true;
            // 現在のスクロール位置と現在の内容の高さを保存
            const previousScrollHeight = chatWindow.scrollHeight; 
            // 追加データを取得
            fetch(`./php/talk_load_more.php?room_id=${room_id}&offset=${offset}`)
                .then(response => response.text())
                .then(data => {
                    // 取得したデータが空でない場合にチャットウィンドウに追加
                    if (data.trim() !== "") {
                        chatWindow.innerHTML = data + chatWindow.innerHTML;
                        // 新しい内容の高さを計算
                        const newScrollHeight = chatWindow.scrollHeight;

                        // 新しい高さの増加分を考慮してスクロール位置を設定
                        chatWindow.scrollTop = newScrollHeight - previousScrollHeight; 
                        offset += 50; // 次のオフセットを設定
 
                    }
                })
                .catch(error => console.error("エラー:", error))
                .finally(() => {
                    loading = false;
                });
        }
    });
});
