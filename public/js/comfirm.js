function deleteHandle(event) {
    //いったんフォームの動きを止める
    event.preventDefault();
    if(window.confirm('本当に削除していいですか？')){
        //削除OKならformを削除
        document.getElementById('delete-form').submit();
    }else {
        alert('キャンセルしました');
    }
}
