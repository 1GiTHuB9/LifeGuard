<?php
class Talk_model {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // チャットルーム名を取得するメソッド
    public function get_room_name($room_id) {
        $sql = "SELECT 
            cr.room_name,
            u.user_name,
            c.comment_detail
        FROM chatrooms cr
        LEFT JOIN comments c ON c.comment_id = cr.comment_id
        LEFT JOIN users u ON c.user_id = u.user_id
        WHERE cr.room_id = :room_id";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':room_id', $room_id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                return [
                    'room_name' => $result['room_name'],
                    'user_name' => $result['user_name'],
                    'comment_detail' => $result['comment_detail']
                ];
            } else {
                return null;
            }
        } catch (PDOException $e) {
            error_log('get_room_name error: ' . $e->getMessage());
            throw $e;
        }
    }

    // ユーザーのチャットルーム情報を取得するメソッド
    public function get_user_room($room_id, $user_id) {
        $sql = "SELECT 
            cr.room_id,
            cr.room_name,
            cr.approval_status,
            u.user_id,
            u.user_name,
            u.profile_img,
            c.comment_detail
        FROM chatrooms cr
        LEFT JOIN comments c ON c.comment_id = cr.comment_id
        LEFT JOIN users u ON c.user_id = u.user_id
        WHERE cr.room_id = :room_id 
        AND (
            cr.approved_by = :user_id 
            OR u.user_id = :user_id
        )
        AND cr.approval_status = 1";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':room_id', $room_id, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                return [
                    'room_id' => $result['room_id'],
                    'room_name' => $result['room_name'],
                    'user_id' => $result['user_id'],
                    'user_name' => $result['user_name'],
                    'profile_img' => $result['profile_img'],
                    'comment_detail' => $result['comment_detail'],
                    'approval_status' => $result['approval_status']
                ];
            } else {
                return null;
            }
        } catch (PDOException $e) {
            error_log('get_user_room error: ' . $e->getMessage());
            throw $e;
        }
    }

    // チャット履歴を取得するメソッド
    public function get_chat($room_id, $user_id, $offset, $limit) {
        $sql = "SELECT 
            c.talk_detail as detail,
            c.user_id,
            c.talk_date,
            'chat' as type,
            NULL as post_detail
        FROM chats c
        WHERE c.room_id = :room_id
        UNION ALL
        SELECT 
            cm.comment_detail as detail,
            cm.user_id,
            cm.comment_date as talk_date,
            'comment' as type,
            p.post_detail
        FROM comments cm
        JOIN posts p ON cm.post_id = p.post_id
        JOIN chatrooms cr ON cr.comment_id = cm.comment_id
        WHERE cr.room_id = :room_id
        ORDER BY talk_date DESC
        LIMIT :limit OFFSET :offset";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':room_id', $room_id, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('get_chat error: ' . $e->getMessage());
            throw $e;
        }
    }

    // チャットを送信するメソッド
    public function send_chat($user_id, $room_id, $message) {
        $sql = "INSERT INTO chats (user_id, room_id, talk_detail, talk_date) 
                VALUES (:user_id, :room_id, :message, NOW())";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindValue(':room_id', $room_id, PDO::PARAM_INT);
            $stmt->bindValue(':message', $message, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log('send_chat error: ' . $e->getMessage());
            throw $e;
        }
    }

    // 承認済みチャットルーム取得
    public function getApprovedChatRooms($userId) {
        $sql = "SELECT 
            cr.room_id,
            cr.room_name,
            c.comment_detail,
            u.profile_img,
            u.user_name,
            ch.talk_detail as latest_message
        FROM chatrooms cr
        LEFT JOIN comments c ON c.comment_id = cr.comment_id
        LEFT JOIN users u ON c.user_id = u.user_id
        LEFT JOIN (
            SELECT room_id, talk_detail
            FROM chats
            WHERE (room_id, talk_date) IN (
                SELECT room_id, MAX(talk_date)
                FROM chats
                GROUP BY room_id
            )
        ) ch ON cr.room_id = ch.room_id
        WHERE cr.approval_status = 1 
        AND (cr.approved_by = :user_id OR u.user_id = :user_id)
        ORDER BY cr.room_date DESC";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('getApprovedChatRooms error: ' . $e->getMessage());
            throw $e;
        }
    }

    // 未承認チャットルーム取得
    public function getPendingChatRooms($userId) {
        $sql = "SELECT 
            cr.room_id,
            cr.room_name,
            c.comment_detail,
            u.user_name,
            u.profile_img,
            p.post_detail
        FROM chatrooms cr
        LEFT JOIN comments c ON c.comment_id = cr.comment_id
        LEFT JOIN users u ON c.user_id = u.user_id
        LEFT JOIN posts p ON c.post_id = p.post_id
        WHERE cr.approval_status = 0 
        AND (
            cr.approved_by = :user_id 
            OR p.user_id = :user_id
        )
        GROUP BY cr.room_id
        ORDER BY cr.room_date DESC";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('getPendingChatRooms error: ' . $e->getMessage());
            throw $e;
        }
    }

    // チャットルームの承認
    public function approveRoom($roomId) {
        $sql = "UPDATE chatrooms SET approval_status = 1 WHERE room_id = :room_id";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':room_id', $roomId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log('approveRoom error: ' . $e->getMessage());
            throw $e;
        }
    }

    // チャットルームの保留
    public function rejectRoom($roomId) {
        $sql = "UPDATE chatrooms SET approval_status = 2 WHERE room_id = :room_id";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':room_id', $roomId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log('rejectRoom error: ' . $e->getMessage());
            throw $e;
        }
    }

    // チャットルームの削除
    public function deleteRoom($roomId) {
        $sql = "DELETE FROM chatrooms WHERE room_id = :room_id";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':room_id', $roomId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log('deleteRoom error: ' . $e->getMessage());
            throw $e;
        }
    }
}
?>