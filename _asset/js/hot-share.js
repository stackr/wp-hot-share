Kakao.init(HotShare.kakaoAPI);
function share_kakaotalk(post_title, post_permalink){
	param = {label: post_title+"\n"+post_permalink}
	Kakao.Link.sendTalkLink(param);
}
function share_kakaostory(post_permalink){
	Kakao.Auth.login({
    	success: function(res) {
    		// 로그인 성공시, API를 호출합니다.
            Kakao.API.request({
              url : '/v1/api/story/linkinfo',
              data : {
                url : post_permalink
              }
            }).then(function(res) {
              // 이전 API 호출이 성공한 경우 다음 API를 호출합니다.
              return Kakao.API.request( {
                url : '/v1/api/story/post/link',
                data : {
                  link_info : res
                }
              });
            }).then(function(res) {
              return Kakao.API.request( {
                url : '/v1/api/story/mystory',
                data : { id : res.id }
              });
            }).then(function(res) {
               alert("해당 URL을 카카오스토리에 공유하였습니다.");
            }, function (err) {
              alert(JSON.stringify(err));
            });
        },
        fail: function(err) {
			alert('카카오스토리 접속이 거부 되었습니다.');
        }
	});
}
