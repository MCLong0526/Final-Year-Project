import{_ as ke,r as u,j as G,G as R,f as h,c as e,w as t,s as n,I as x,F as H,o as p,n as r,U,e as $,y as b,h as j,V as y,B as S,A as k,q as we,t as Ve,k as Pe,l as Ce}from"./main-q3bGupOw.js";import{V as ne,r as re}from"./VForm-Dw0qmD9W.js";import{_ as Ie}from"./chat-B5AiBdXx.js";import{S as ie}from"./ShowPosts-B9x43Hwg.js";import{u as xe}from"./AuthStore-Ds0lMCMH.js";import{l as ue}from"./lodash-BYTxXjAY.js";import{V as w,a as i,b as O,c as de,d as Y,f as Ue,h as be}from"./VCard-MDPe6KQD.js";import{a as ce,V as Se}from"./VDialog-CWkvVDyV.js";import{V as De}from"./VSnackbar-CvGjGO61.js";import{V as pe}from"./VImg-Bi5hqBb2.js";import{V as K}from"./VTextField-CFs2oFps.js";import{V as Ee,a as Fe}from"./VChip-3J3Tae1Z.js";import{V as J}from"./VTooltip-CcIo_GfK.js";import"./forwardRefs-4ZfUsaKb.js";import"./picker-DE4OueUM.js";import"./VContainer-EdaonRC9.js";import"./VAlert-FIbEPfVh.js";import"./VMenu-CKrTKVZz.js";import"./VOverlay-CsgvhYn4.js";import"./_commonjsHelpers-BosuxZz1.js";import"./ssrBoot-g9LnkWFy.js";const d=D=>(Pe("data-v-240ad23b"),D=D(),Ce(),D),Le={class:"box-style"},$e={class:"d-flex align-items-center"},ze={class:"upload-container"},Te=d(()=>r("p",{class:"upload-text"},"Drag & Drop your picture here.",-1)),Ae=d(()=>r("p",{class:"upload-text"},"Click here to upload picture (PNG, JPEG, JPG).",-1)),Be={key:0,class:"uploaded-images"},Ne=d(()=>r("p",{class:"upload-text"},"Selected picture: ",-1)),Ge=["src"],Re=d(()=>r("span",{class:"ml-2"},"Upload Image",-1)),He=d(()=>r("span",null,"Click here to upload image!",-1)),Je=d(()=>r("span",{class:"ml-2"},"Create Post ",-1)),We=d(()=>r("span",null,"Click here to add new post!",-1)),qe={class:"box-style"},Me=d(()=>r("span",{class:"ml-2"},"Load More",-1)),je=d(()=>r("label",{for:"firstNameHorizontalIcons"},"Post Content",-1)),Oe=d(()=>r("label",{for:"emailHorizontalIcons"},"Post Image",-1)),Ye={class:"upload-container"},Ke=d(()=>r("p",{class:"upload-text"},"Drag & Drop your picture here.",-1)),Qe=d(()=>r("p",{class:"upload-text"},"Click here to upload picture (PNG, JPEG, JPG).",-1)),Xe={key:0,class:"uploaded-images"},Ze=d(()=>r("p",{class:"upload-text"},"Selected picture: ",-1)),et=["src"],tt=d(()=>r("span",null,"Click here to upload image",-1)),lt=d(()=>r("span",null,"Click here to view all posts!",-1)),at=d(()=>r("span",{class:"ml-2"},"All Posts",-1)),ot=d(()=>r("span",null,"Post created successfully!",-1)),st={__name:"life-moment-post",props:{relatedId:{type:String,default:""}},emits:["update:relatedId"],setup(D,{emit:nt}){const E=D,Q=xe(),W=u([]),z=u(!1),C=u(!1),X=u(5);let T=1;const A=u(!1),c=u(""),m=u([]),f=u([]),v=u(""),Z=u("all"),B=u(!1),V=u([]),F=u([]),I=u(!1),P=u({}),L=u(!1),fe=async()=>{try{const s=localStorage.getItem("token");if(!s)throw new Error("Token not found");const l=await G.get("/api/auth/get-user-by-token",{headers:{Authorization:`Bearer ${s}`}});W.value=l.data.user}catch(s){console.error(s)}},ee=s=>{s.preventDefault();const l=s.dataTransfer.files;te(l)},te=s=>{const l=s[0];if(l.type.startsWith("image/")){const o=new FileReader;o.onload=a=>{m.value=[{url:a.target.result,file:l}]},o.readAsDataURL(l)}},N=u(null),le=()=>{N.value&&N.value.click()},ae=s=>{const l=s.target.files;te(l)},oe=s=>{m.value.splice(s,1)},se=()=>{G.post("/api/posts/store",{content:c.value,picture:m.value}).then(s=>{z.value=!1,c.value=null,m.value=[],f.value=[],B.value=!0,g()}).catch(s=>{console.log(s)})},g=ue.debounce(()=>{let s="/api/posts?page="+T+"&per_page="+X.value;v.value&&v.value.length>2&&v.value!==null&&(s+="&search="+v.value),E.relatedId&&(f.value=[],s+="&related_id="+E.relatedId),G.get(s).then(({data:l})=>{const o=l.data.unrelated_posts.data;o.forEach(a=>{a.user.avatar&&(a.user.avatar="http://127.0.0.1:8000/storage/"+a.user.avatar)}),o.forEach(a=>{a.picture&&(a.picture="http://127.0.0.1:8000/storage/"+a.picture)}),o.forEach(a=>{a.created_at=new Date(a.created_at).toLocaleString("en-US",{year:"numeric",month:"long",day:"numeric",hour:"numeric",minute:"numeric"})}),o.forEach(a=>{a.likes_count=a.likes.length}),o.forEach(a=>{a.is_liked=a.likes.some(_=>_.user_id===Q.user.user_id)}),f.value=[...f.value,...o],f.value=f.value.reverse().filter((a,_,he)=>_===he.findIndex(ye=>ye.post_id===a.post_id)).reverse(),E.relatedId&&(P.value=l.data.related_posts.data,P.value.forEach(a=>{a.picture&&(a.picture="http://127.0.0.1:8000/storage/"+a.picture)}),P.value.forEach(a=>{a.user.avatar&&(a.user.avatar="http://127.0.0.1:8000/storage/"+a.user.avatar)}),P.value.forEach(a=>{a.created_at=new Date(a.created_at).toLocaleString("en-US",{year:"numeric",month:"long",day:"numeric",hour:"numeric",minute:"numeric"})}),P.value.forEach(a=>{a.likes_count=a.likes.length}),P.value.forEach(a=>{a.is_liked=a.likes.some(_=>_.user_id===Q.user.user_id)}),L.value=!0)}).catch(l=>{console.log(l)})},500),me=()=>window.innerHeight+window.scrollY<=document.body.offsetHeight;let q=!1;const ve=()=>{!q&&me()&&f.value.length%X.value===0&&(q=!0,T+=1,A.value=!0,setTimeout(()=>{g(),q=!1,A.value=!1},1500))},ge=()=>{T+=1,g()},M=()=>{G.get("/api/users/get-following").then(s=>{V.value=s.data.data}).catch(s=>{console.log(s)})};window.addEventListener("scroll",ve),R(v,ue.debounce(()=>{T=1,f.value=[],g()},500)),R(c,()=>{setTimeout(()=>{if(c.value.charAt(c.value.length-1)==="@")I.value=!0,F.value=V.value;else if(c.value.includes("@")&&I.value===!1&&F.value.length>0){I.value=!0;const s=c.value.split("@").slice(-1)[0].split(" ")[0].toLowerCase();F.value=V.value.filter(l=>l.username.toLowerCase().includes(s))}else I.value=!1},500)});const _e=s=>{const l=c.value.split("@").slice(0,-1).join("@");c.value=`${l}@${s.username} `,F.value=[],I.value=!1};return R(()=>E.relatedId,s=>{s&&g()}),R(()=>E.relatedId,s=>{s===""&&window.location.reload()}),g(),fe(),M(),(s,l)=>(p(),h(H,null,[e(w,null,{default:t(()=>[e(i,{cols:"12",md:"8"},{default:t(()=>[r("div",Le,[e(O,null,{default:t(()=>[e(n(ne),{ref:"refForm",onSubmit:l[4]||(l[4]=U(()=>{},["prevent"]))},{default:t(()=>[e(w,null,{default:t(()=>[e(i,{cols:"12"},{default:t(()=>[e(w,{"no-gutters":""},{default:t(()=>[e(i,{cols:"12",md:"1"},{default:t(()=>[r("div",$e,[n(W).avatar?(p(),$(de,{key:0,size:"50"},{default:t(()=>[e(pe,{src:n(W).avatar},null,8,["src"])]),_:1})):b("",!0)])]),_:1}),e(i,{cols:"12",md:"11"},{default:t(()=>[e(K,{modelValue:n(c),"onUpdate:modelValue":l[0]||(l[0]=o=>x(c)?c.value=o:null),"prepend-inner-icon":"ri-message-2-line",rounded:"",placeholder:"What's on your mind?",rules:[n(re)]},null,8,["modelValue","rules"]),n(I)&&n(V).length>0?(p(),$(Ee,{key:0,style:{"max-block-size":"80px","overflow-y":"auto"}},{default:t(()=>[(p(!0),h(H,null,j(n(F),(o,a)=>(p(),$(Fe,{key:a,onClick:_=>_e(o)},{default:t(()=>[e(de,{size:"32",color:o.avatar?"":"primary",class:we(`${o.avatar?"":"v-avatar-light-bg primary--text"}`),variant:o.avatar?void 0:"tonal",style:{"margin-inline-end":"8px"}},{default:t(()=>[e(pe,{src:o.avatar},null,8,["src"])]),_:2},1032,["color","class","variant"]),S(" "+Ve(o.username),1)]),_:2},1032,["onClick"]))),128))]),_:1})):b("",!0)]),_:1})]),_:1})]),_:1}),n(C)===!0?(p(),$(i,{key:0,cols:"12"},{default:t(()=>[e(w,{"no-gutters":""},{default:t(()=>[e(i,{cols:"12",md:"3"}),e(i,{cols:"12",md:"8"},{default:t(()=>[r("div",ze,[r("input",{type:"file",ref_key:"fileInput",ref:N,style:{display:"none"},accept:"image/*",onChange:ae},null,544),r("div",{class:"upload-box",onDrop:U(ee,["prevent"]),onDragover:l[1]||(l[1]=U(()=>{},["prevent"])),onClick:le},[e(y,{class:"upload-icon"},{default:t(()=>[S("ri-image-add-line")]),_:1}),Te,Ae,n(m).length>0?(p(),h("div",Be,[Ne,(p(!0),h(H,null,j(n(m),(o,a)=>(p(),h("div",{key:a,class:"uploaded-image"},[r("img",{src:o.url,alt:"Uploaded Image"},null,8,Ge),e(k,{onClick:_=>oe(a),icon:"ri-close-line",class:"remove-btn"},null,8,["onClick"])]))),128))])):b("",!0)],32)])]),_:1})]),_:1})]),_:1})):b("",!0),e(i,{"offset-md":"3",cols:"12",md:"9",class:"d-flex gap-4"},{default:t(()=>[e(k,{color:"secondary",variant:"tonal",onClick:l[2]||(l[2]=o=>C.value=!n(C))},{default:t(()=>[e(y,{icon:"ri-chat-upload-line",size:"20"}),Re,e(J,{"open-delay":"500",location:"top",activator:"parent",transition:"scroll-y-transition"},{default:t(()=>[He]),_:1})]),_:1}),e(k,{type:"submit",onClick:l[3]||(l[3]=o=>s.$refs.refForm.validate().then(()=>se()))},{default:t(()=>[e(y,{icon:"ri-send-plane-2-line",size:"20"}),Je,e(J,{"open-delay":"500",location:"top",activator:"parent",transition:"scroll-y-transition"},{default:t(()=>[We]),_:1})]),_:1})]),_:1})]),_:1})]),_:1},512)]),_:1})]),r("div",qe,[e(w,{class:"mt-2 ml-2 mr-2"},{default:t(()=>[e(i,{cols:"12",md:"7"},{default:t(()=>[e(k,{onClick:l[5]||(l[5]=o=>ge()),color:"primary",class:"mb-2"},{default:t(()=>[e(y,{icon:"ri-arrow-down-s-line",size:"20"}),Me]),_:1})]),_:1}),e(i,{cols:"12",md:"5"},{default:t(()=>[e(K,{modelValue:n(v),"onUpdate:modelValue":l[6]||(l[6]=o=>x(v)?v.value=o:null),label:"Search",clearable:"","prepend-inner-icon":"ri-search-line",placeholder:"Placeholder Text"},null,8,["modelValue"])]),_:1})]),_:1}),e(ie,{posts:n(f),getPosts:n(g),getFollowingUsers:M,showLoading:n(A),typePosts:n(Z),followingUsers:n(V)},null,8,["posts","getPosts","showLoading","typePosts","followingUsers"])])]),_:1}),e(i,{cols:"12",md:"4"},{default:t(()=>[e(Y,{title:"Followed Users"},{default:t(()=>[e(Ie,{followingUsers:n(V)},null,8,["followingUsers"])]),_:1})]),_:1})]),_:1}),e(ce,{modelValue:n(z),"onUpdate:modelValue":l[12]||(l[12]=o=>x(z)?z.value=o:null),"max-width":"600"},{default:t(()=>[e(Y,{title:"New Post Information"},{default:t(()=>[e(O,null,{default:t(()=>[e(n(ne),{ref:"refForm",onSubmit:l[11]||(l[11]=U(()=>{},["prevent"]))},{default:t(()=>[e(w,null,{default:t(()=>[e(i,{cols:"12"},{default:t(()=>[e(w,{"no-gutters":""},{default:t(()=>[e(i,{cols:"12",md:"3"},{default:t(()=>[je]),_:1}),e(i,{cols:"12",md:"9"},{default:t(()=>[e(K,{modelValue:n(c),"onUpdate:modelValue":l[7]||(l[7]=o=>x(c)?c.value=o:null),"prepend-inner-icon":"ri-message-2-line",placeholder:"What's on your mind?",rules:[n(re)]},null,8,["modelValue","rules"])]),_:1})]),_:1})]),_:1}),n(C)===!0?(p(),$(i,{key:0,cols:"12"},{default:t(()=>[e(w,{"no-gutters":""},{default:t(()=>[e(i,{cols:"12",md:"3"},{default:t(()=>[Oe]),_:1}),e(i,{cols:"12",md:"9"},{default:t(()=>[r("div",Ye,[r("input",{type:"file",ref_key:"fileInput",ref:N,style:{display:"none"},accept:"image/*",onChange:ae},null,544),r("div",{class:"upload-box",onDrop:U(ee,["prevent"]),onDragover:l[8]||(l[8]=U(()=>{},["prevent"])),onClick:le},[e(y,{class:"upload-icon"},{default:t(()=>[S("ri-image-add-line")]),_:1}),Ke,Qe,n(m).length>0?(p(),h("div",Xe,[Ze,(p(!0),h(H,null,j(n(m),(o,a)=>(p(),h("div",{key:a,class:"uploaded-image"},[r("img",{src:o.url,alt:"Uploaded Image"},null,8,et),e(k,{onClick:_=>oe(a),icon:"ri-close-line",class:"remove-btn"},null,8,["onClick"])]))),128))])):b("",!0)],32)])]),_:1})]),_:1})]),_:1})):b("",!0),e(i,{"offset-md":"3",cols:"12",md:"9",class:"d-flex gap-4"},{default:t(()=>[e(k,{color:"secondary",variant:"tonal",onClick:l[9]||(l[9]=o=>C.value=!n(C))},{default:t(()=>[e(y,{icon:"ri-chat-upload-line",size:"20"}),e(J,{"open-delay":"500",location:"top",activator:"parent",transition:"scroll-y-transition"},{default:t(()=>[tt]),_:1})]),_:1}),e(k,{type:"submit",onClick:l[10]||(l[10]=o=>s.$refs.refForm.validate().then(()=>se()))},{default:t(()=>[S(" Post ")]),_:1})]),_:1})]),_:1})]),_:1},512)]),_:1})]),_:1})]),_:1},8,["modelValue"]),e(ce,{modelValue:n(L),"onUpdate:modelValue":l[14]||(l[14]=o=>x(L)?L.value=o:null),"max-width":"600",persistent:""},{default:t(()=>[e(Y,null,{default:t(()=>[e(Ue,{class:"text-overline text-center mt-2",style:{"font-size":"20px !important"}},{default:t(()=>[S(" Related Post ")]),_:1}),e(O,{style:{"block-size":"260px","overflow-y":"auto"}},{default:t(()=>[e(ie,{posts:n(P),getPosts:n(g),getFollowingUsers:M,showLoading:n(A),typePosts:n(Z),followingUsers:n(V)},null,8,["posts","getPosts","showLoading","typePosts","followingUsers"])]),_:1}),e(be,null,{default:t(()=>[e(Se),e(k,{to:{name:"life-moment-post",params:{relatedId:""}},color:"primary",onClick:l[13]||(l[13]=o=>L.value=!1),class:"ma-2"},{default:t(()=>[e(J,{"open-delay":"500",location:"top",activator:"parent",transition:"scroll-y-transition"},{default:t(()=>[lt]),_:1}),e(y,{icon:"ri-chat-3-line",size:"20"}),at]),_:1})]),_:1})]),_:1})]),_:1},8,["modelValue"]),e(De,{modelValue:n(B),"onUpdate:modelValue":l[15]||(l[15]=o=>x(B)?B.value=o:null),location:"top end",transition:"scale-transition",color:"success"},{default:t(()=>[e(y,{size:"20",class:"me-2"},{default:t(()=>[S("ri-checkbox-circle-line")]),_:1}),ot]),_:1},8,["modelValue"])],64))}},bt=ke(st,[["__scopeId","data-v-240ad23b"]]);export{bt as default};
