let currentMonth = new Date().getMonth();
let currentYear = new Date().getFullYear();
let selectedDateCell = null;
let reactionsData = {};

function changeMonth(offset) {
    currentMonth += offset;

    // 年が変わる場合の処理
    if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
    } else if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
    }

    loadReactions(currentYear, currentMonth); // 指定の年月のリアクションを読み込み
}


//サーバーから反応データを取得してカレンダーに反映
function loadReactions(year, month) {
    const formattedMonth = String(month + 1).padStart(2, '0');
    const startDate = `${year}-${formattedMonth}-01`;
    const endDate = `${year}-${formattedMonth}-31`;
    
    fetch(`./php/get_reactions.php?start_date=${startDate}&end_date=${endDate}`)
        .then(response => {
            if(!response.ok){
                throw new Error('ネットワーク応答が不正です');
            }
            return response.json();
        })
        .then(data => {
            reactionsData = {};
            data.forEach(item => {
                reactionsData[item.reaction_date] = item.reaction;
            });
            createCalendar(currentYear, currentMonth);
        })
        .catch(error => {
            console.error('データ取得エラー:', error);
            createCalendar(currentYear,currentMonth);
        });
}

function createCalendar(year, month) {
    const calendarDiv = document.getElementById('calendars');
    calendarDiv.innerHTML = ''; // カレンダーをリセット

    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    const weekdays = ['日', '月', '火', '水', '木', '金', '土'];

    document.getElementById('current-month-year').innerText = `${year}年 ${month + 1}月`;

    let calendarTable = '<table>';
    calendarTable += '<tr>';

    weekdays.forEach((day, index) => {
        calendarTable += `<th class="${index === 0 ? 'sunday' : index === 6 ? 'saturday' : ''}">${day}</th>`;
    });
    calendarTable += '</tr><tr>';

    for (let i = 0; i < firstDay; i++) {
        calendarTable += '<td class="empty"></td>';
    }

    for (let day = 1; day <= daysInMonth; day++) {
        if ((firstDay + day - 1) % 7 === 0 && day !== 1) {
            calendarTable += '</tr><tr>';
        }

        let dayOfWeek = (firstDay + day - 1) % 7;
        let date = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

        let reactionText = reactionsData[date] || '';

        calendarTable += `<td class="${dayOfWeek === 0 ? 'sunday' : dayOfWeek === 6 ? 'saturday' : ''}" onclick="openModal(this, '${date}')">
                            <div class="date-number">${day}</div>
                            <div class="reaction">${reactionText}</div>
                          </td>`;
    }

    calendarTable += '</tr></table>';
    calendarDiv.innerHTML = calendarTable;
}

// 反応をサーバーに保存
function saveReaction(date, reaction) {
    fetch('./php/save_reaction.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `date=${encodeURIComponent(date)}&reaction=${encodeURIComponent(reaction)}`,
    })
        .then(response => response.text())
        .then(data => {
            console.log(data);

            reactionsData[date] = reaction;
            selectedDateCell.querySelector('.reaction').innerText = reaction;
            // loadReactions();
        })
        .catch(error => console.error('保存エラー:', error));
}

function openModal(dateCell, date) {
    selectedDateCell = dateCell;
    selectedDate = date;
    document.getElementById('reaction-modal').style.display = 'block';
}

function selectReaction(reaction) {
    if (selectedDateCell) {
        // selectedDateCell.querySelector('.reaction').innerText = reaction;
        saveReaction(selectedDate, reaction);
        closeModal();
    }
}

function closeModal() {
    document.getElementById('reaction-modal').style.display = 'none';
}

window.onload = function() {
    loadReactions();
};