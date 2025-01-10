function goBack() {
    location.href = "home.php";
}    
// チャットルームの承認、保留、削除の確認
function confirmApproval(roomId) {
    if (confirm("このチャットルームを承認しますか？\nユーザー名が相手に公開されます。")) {
        location.href = "./php/talk_approve.php?room_id=" + roomId;
    }
}

function confirmRejection(roomId) {
    if (confirm("このチャットルームを保留しますか？")) {
        location.href = "./php/talk_reject.php?room_id=" + roomId;
    }
}

function confirmDeletion(roomId) {
    if (confirm("このチャットルームを削除しますか？")) {
        location.href = "./php/talk_delete.php?room_id=" + roomId;
    }
}