<?php
    class Talk_model{
        private $pdo;
        public function __construct($pdo){
            $this->pdo = $pdo;
        }
        // チャットルーム名を取得
        public function get_room_name($room_id){
            $stmt = $this->pdo->prepare("
                SELECT room_name 
                FROM chatrooms 
                WHERE room_id = ?
            ");
            $stmt->execute([$room_id]);
            $room = $stmt->fetch(PDO::FETCH_ASSOC) ?? null;
            return $room;
        }
        //チャットルーム一覧を取得
        public function getChatRooms() {
            $stmt = $this->pdo->prepare("SELECT room_name, room_id FROM chatrooms");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        //トーク一覧画面で表示する最新メッセージを取得
        public function getLatestMessages() {
            $stmt = $this->pdo->prepare("
                SELECT c.room_id, c.talk_detail
                FROM chats c
                JOIN (
                    SELECT room_id, MAX(message_id) AS max_message_id
                    FROM chats
                    GROUP BY room_id
                ) AS max_messages
                ON c.room_id = max_messages.room_id AND c.message_id = max_messages.max_message_id
                ORDER BY c.room_id ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        //チャットルームのユーザー情報を取得
        public function get_user_room($room_id){
            $stmt = $this->pdo->prepare("
                SELECT user1_id, user2_id
                FROM userrooms
                WHERE room_id = ?
            ");
            $stmt->execute([$room_id]);
            $userroom = $stmt->fetch(PDO::FETCH_ASSOC);
            return $userroom;
        }
        //チャット内容を取得
        public function get_chat($room_id, $user1_id, $offset, $limit, $user2_id){
            $stmt = $this->pdo->prepare("
                SELECT * FROM (

                SELECT 
                    c.comment_date AS date,
                    c.comment_detail AS detail,
                    -- 投稿内容を取得
                    p.post_detail AS post_detail,
                    c.user_id,
                    -- コメントかチャットかを判別するための type カラム
                    'comment' AS type
                FROM comments c
                -- comments テーブルと posts テーブルを post_id をキーに結合します。
                -- これにより、コメントの対象となる投稿の情報も取得できます。
                JOIN posts p ON c.post_id = p.post_id
                -- 自分が user1 で相手が user2 の場合、またはその逆の場合のコメントを取得
                WHERE (c.user_id = :user1_id AND p.user_id = :user2_id)
                   OR (c.user_id = :user2_id AND p.user_id = :user1_id)
                
                UNION ALL
                
                SELECT 
                    ch.talk_date AS date,
                    ch.talk_detail AS detail,
                    -- チャットには投稿の情報がないため、固定値 NULL を設定。
                    NULL AS post_detail,
                    ch.user_id,
                    -- コメントかチャットかを判別するための type カラム
                    'chat' AS type
                FROM chats ch
                WHERE ch.room_id = :room_id
                
                ) AS combined
                ORDER BY date DESC
                LIMIT :limit OFFSET :offset
            ");
            
            $stmt->bindValue(':user1_id', $user1_id, PDO::PARAM_INT);
            $stmt->bindValue(':user2_id', $user2_id, PDO::PARAM_INT);
            $stmt->bindValue(':room_id', $room_id, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
    
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        }
        //チャット内容を送信
        public function send_chat($user_id, $room_id, $talk_detail) {
            try {
                $sql = "INSERT INTO chats (user_id, room_id, talk_detail, talk_date)
                        VALUES (:user_id, :room_id, :talk_detail, NOW())";
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->bindParam(':room_id', $room_id, PDO::PARAM_INT);
                $stmt->bindParam(':talk_detail', $talk_detail, PDO::PARAM_STR);
    
                return $stmt->execute();
            } catch (PDOException $e) {
                throw new Exception("メッセージ送信エラー: " . $e->getMessage());
            }
        }
        // 未承認のチャットルームを取得
        public function getPendingChatRooms($userId) {
            $stmt = $this->pdo->prepare("
                SELECT cr.room_id, cr.room_name, p.post_detail, c.comment_detail, u.user_name
                FROM chatrooms cr
                JOIN comments c ON cr.comment_id = c.comment_id
                JOIN posts p ON c.post_id = p.post_id
                JOIN users u ON c.user_id = u.user_id
                WHERE cr.approval_status = 0 AND p.user_id = :user_id
            ");
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // 承認済みのチャットルームを取得
        public function getApprovedChatRooms($userId) {
            $stmt = $this->pdo->prepare("
                SELECT cr.room_id, cr.room_name
                FROM chatrooms cr
                JOIN userrooms ur ON cr.room_id = ur.room_id
                WHERE cr.approval_status = 1 AND (ur.user1_id = :user_id OR ur.user2_id = :user_id)
            ");
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        // 拒否されたチャットルームを取得
        public function getRejectedChatRooms($userId) {
            $stmt = $this->pdo->prepare("
                SELECT cr.room_id, cr.room_name, p.post_detail, c.comment_detail, u.user_name
                FROM chatrooms cr
                JOIN comments c ON cr.comment_id = c.comment_id
                JOIN posts p ON c.post_id = p.post_id
                JOIN users u ON c.user_id = u.user_id
                WHERE cr.approval_status = 2 AND p.user_id = :user_id
            ");
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
    }
?>