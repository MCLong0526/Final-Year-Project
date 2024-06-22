import{_ as De}from"./UserProfileDialog-Cpf-5CKy.js";import{_ as me,r as y,o as f,f as R,e as O,w as s,n as t,c as e,V as i,B as r,F as X,h as se,t as p,y as N,q as be,A as Y,k as _e,l as fe,s as m,I as F,U as ke,j as ae,S as he,d as Oe,O as Ce,G as Te,a9 as ge}from"./main-q3bGupOw.js";import{d as w,b as ie,V as Ie,a as we}from"./VChip-3J3Tae1Z.js";import{c as te,d as ce,f as le,b as J,V as Z,a as z,e as Me,g as Pe}from"./VCard-MDPe6KQD.js";import{V as K}from"./VImg-Bi5hqBb2.js";import{V as ue}from"./VTooltip-CcIo_GfK.js";import{V as re}from"./VTable-CWmaFE2C.js";import{V as ve}from"./VAlert-FIbEPfVh.js";import{a as ne}from"./VDialog-CWkvVDyV.js";import{V as pe}from"./VSnackbar-CvGjGO61.js";import{V as Re,r as xe}from"./VForm-Dw0qmD9W.js";import{V as Ae,a as ze,b as Se}from"./VTextarea-ClfgZcbR.js";import{V as Ve}from"./VTextField-CFs2oFps.js";import{l as Ee}from"./logo-EPB3ctmV.js";import{d as je,E as Fe,u as de,w as Ue,P as He}from"./xlsx-BXC1t0wU.js";import{V as Ne}from"./VMenu-CKrTKVZz.js";import{l as Ye}from"./lodash-BYTxXjAY.js";import{V as qe,a as Be,b as ye,c as $e}from"./VWindowItem-7FQGX8rp.js";import{V as Le}from"./VCombobox-CXYxe5On.js";import"./chat-B5AiBdXx.js";import"./AuthStore-Ds0lMCMH.js";import"./picker-DE4OueUM.js";import"./VContainer-EdaonRC9.js";import"./forwardRefs-4ZfUsaKb.js";import"./ssrBoot-g9LnkWFy.js";import"./VOverlay-CsgvhYn4.js";import"./_commonjsHelpers-BosuxZz1.js";import"./VCheckboxBtn-Bsz_ZLvw.js";const U=C=>(_e("data-v-ec2f57d6"),C=C(),fe(),C),We={class:"text-uppercase text-center"},Qe={class:"text-uppercase text-center"},Ge={class:"text-uppercase text-center"},Je={class:"text-uppercase text-center"},Ke={class:"text-uppercase text-center"},Xe={class:"text-uppercase text-center"},Ze={class:"text-uppercase text-center"},et={class:"text-center"},tt={class:"d-flex align-items-center"},st={style:{color:"#6c757d","font-size":"12px"}},at={class:"d-flex align-items-center"},lt={style:{color:"#6c757d","font-size":"12px"}},rt={class:"text-center"},nt={class:"text-center"},ot={class:"text-center"},it={class:"text-center"},dt=U(()=>t("span",null,"View Details",-1)),ct={class:"text-uppercase text-center"},ut={class:"text-uppercase text-center"},pt={class:"text-uppercase text-center"},mt={class:"text-uppercase text-center"},_t={class:"text-uppercase text-center"},ft={class:"text-uppercase text-center"},vt={class:"text-uppercase text-center"},ht={colspan:"8",class:"text-center"},gt={class:"detail-row"},xt=U(()=>t("strong",null,"Order ID",-1)),St={class:"detail-row"},yt=U(()=>t("strong",null,"Order Date Time",-1)),$t={class:"detail-row"},bt=U(()=>t("strong",null,"Customer",-1)),Vt={class:"detail-row"},Dt=U(()=>t("strong",null,"Status",-1)),kt={key:0},Ot={key:1},Ct={class:"detail-row"},Tt=U(()=>t("strong",null,"Service Name",-1)),It={class:"detail-row"},wt=U(()=>t("strong",null,"Service Date Time",-1)),Mt={key:0},Pt={key:1},Rt={class:"detail-row"},At=U(()=>t("strong",null,"Service Duration",-1)),zt={key:0},Et={key:1},jt={class:"detail-row"},Ft=U(()=>t("strong",null,"Approximated Price",-1)),Ut={key:0},Ht={key:1},Nt={class:"detail-row"},Yt=U(()=>t("strong",null,"Place to service",-1)),qt={class:"detail-row",style:{display:"flex","align-items":"flex-start"}},Bt=U(()=>t("strong",null,"Customer Remark",-1)),Lt={style:{"white-space":"pre-line"}},Wt={class:"detail-row",style:{display:"flex","align-items":"flex-start"}},Qt=U(()=>t("strong",null,"Provider Remark",-1)),Gt={style:{"white-space":"pre-line"}},Jt={__name:"OrderServiceConfirmedTable",props:{confirmedOrders:{type:Object,required:!0},getConfirmedOrders:{type:Function,required:!0}},setup(C){const v=y({}),E=y(!1),g=y(!1),h=y(!1),T=y({}),D=M=>{E.value=!0,v.value=M},I=M=>{T.value=M,h.value=!0},j=M=>{const $=new Date;if(!M)return!0;const u=new Date(M.split(" ")[0]);return u.getFullYear()===$.getFullYear()&&u.getMonth()===$.getMonth()&&u.getDate()===$.getDate()?!1:u<$};return(M,$)=>(f(),R(X,null,[C.confirmedOrders.length>0?(f(),O(re,{key:0,height:"250","fixed-header":""},{default:s(()=>[t("thead",null,[t("tr",null,[t("th",We,[e(i,{icon:"ri-info-i"}),r(" Order ID ")]),t("th",Qe,[e(i,{icon:"ri-product-hunt-line"}),r(" Service ")]),t("th",Ge,[e(i,{icon:"ri-id-card-line"}),r(" Customer ")]),t("th",Je,[e(i,{icon:"ri-calendar-schedule-line"}),r(" Order Date ")]),t("th",Ke,[e(i,{icon:"ri-calendar-schedule-line"}),r(" Service Date Time ")]),t("th",Xe,[e(i,{icon:"ri-verified-badge-line"}),r(" Status ")]),t("th",Ze,[e(i,{icon:"ri-eye-line"}),r(" View ")])])]),t("tbody",null,[(f(!0),R(X,null,se(C.confirmedOrders,u=>(f(),R("tr",{key:u.id},[t("td",et,[j(u.service_dateTime)==!0?(f(),O(w,{key:0,color:"secondary"},{default:s(()=>[r(p(u.id),1)]),_:2},1024)):(f(),O(w,{key:1,color:"primary"},{default:s(()=>[r(p(u.id),1)]),_:2},1024))]),t("td",null,[t("div",tt,[u.service.pictures.length>0&&u.service.pictures?(f(),O(te,{key:0,size:"32",rounded:"sm",style:{"margin-inline-end":"8px"}},{default:s(()=>[e(K,{src:u.service.pictures[0].picture_path},null,8,["src"])]),_:2},1024)):N("",!0),t("div",null,[t("div",null,p(u.service.name),1),t("div",st,p(u.service.type),1)])])]),t("td",null,[t("div",at,[u.user.avatar?(f(),O(te,{key:0,size:"32",color:u.user.avatar?"":"primary",class:"mr-1"},{default:s(()=>[e(K,{src:u.user.avatar},null,8,["src"])]),_:2},1032,["color"])):N("",!0),t("div",null,[t("div",null,p(u.user.username),1),t("div",lt,p(u.user.email),1)])])]),t("td",rt,p(u.order_dateTime),1),t("td",nt,[u.status=="Approved"?(f(),O(w,{key:0,color:"warning",size:"x-small"},{default:s(()=>[r(p(u.service_dateTime),1)]),_:2},1024)):(f(),O(w,{key:1,color:"warning",size:"x-small"},{default:s(()=>[r("Not Set")]),_:1}))]),t("td",ot,[t("div",{class:be(["status-dot",{"status-dot-read":u.status==="Rejected","status-dot-read2":u.status==="Cancelled"}])},[u.status=="Approved"?(f(),O(w,{key:0,color:"success",size:"x-small"},{default:s(()=>[r(p(u.status),1)]),_:2},1024)):N("",!0),u.status=="Rejected"?(f(),O(w,{key:1,color:"error",size:"x-small"},{default:s(()=>[r(p(u.status),1)]),_:2},1024)):N("",!0),u.status=="Cancelled"?(f(),O(w,{key:2,color:"secondary",size:"x-small"},{default:s(()=>[r(p(u.status),1)]),_:2},1024)):N("",!0)],2)]),t("td",it,[e(Y,{color:"primary",style:{"margin-inline":"15px 3px"},size:"small",onClick:Q=>D(u)},{default:s(()=>[e(i,{icon:"ri-list-view"}),e(ue,{"open-delay":"500",location:"top",activator:"parent",transition:"scroll-y-transition"},{default:s(()=>[dt]),_:1})]),_:2},1032,["onClick"])])]))),128))])]),_:1})):(f(),O(re,{key:1,height:"140","fixed-header":""},{default:s(()=>[t("thead",null,[t("tr",null,[t("th",ct,[e(i,{icon:"ri-info-i"}),r(" Order ID ")]),t("th",ut,[e(i,{icon:"ri-product-hunt-line"}),r(" Service ")]),t("th",pt,[e(i,{icon:"ri-id-card-line"}),r(" Customer ")]),t("th",mt,[e(i,{icon:"ri-calendar-schedule-line"}),r(" Order Date ")]),t("th",_t,[e(i,{icon:"ri-calendar-schedule-line"}),r(" Meet Date ")]),t("th",ft,[e(i,{icon:"ri-verified-badge-line"}),r(" Status ")]),t("th",vt,[e(i,{icon:"ri-eye-line"}),r(" View ")])])]),t("tbody",null,[t("tr",null,[t("td",ht,[e(ve,{variant:"tonal",type:"warning",class:"mt-2",color:"primary",closable:"",dense:""},{default:s(()=>[r(" No confirmed orders found. ")]),_:1})])])])]),_:1})),e(ne,{modelValue:E.value,"onUpdate:modelValue":$[2]||($[2]=u=>E.value=u),scrollable:"","max-width":"550"},{default:s(()=>[e(ce,null,{default:s(()=>[e(le,{class:"mt-2 mb-1"},{default:s(()=>[r("Order Details")]),_:1}),e(ie,{class:"mb-2"}),e(J,{style:{"max-block-size":"400px","overflow-y":"auto"}},{default:s(()=>[t("div",gt,[xt,t("span",null,": #"+p(v.value.id),1)]),t("div",St,[yt,t("span",null,": "+p(v.value.order_dateTime),1)]),t("div",$t,[bt,t("span",null,": "+p(v.value.user.username),1)]),t("div",Vt,[Dt,v.value.status=="Approved"?(f(),R("span",kt,[r(": "),e(w,{color:"success",size:"small"},{default:s(()=>[e(i,{icon:"ri-check-fill",class:"mr-1"}),r(p(v.value.status),1)]),_:1})])):(f(),R("span",Ot,[r(": "),e(w,{color:"error",size:"small"},{default:s(()=>[e(i,{icon:"ri-close-line",class:"mr-1"}),r(p(v.value.status),1)]),_:1})]))]),e(ie,{class:"mt-2 mb-2"}),t("div",Ct,[Tt,t("span",null,": "+p(v.value.service.name),1)]),t("div",It,[wt,v.value.status=="Approved"?(f(),R("span",Mt,[r(": "),e(w,{color:"warning",size:"small"},{default:s(()=>[e(i,{icon:"ri-check-double-fill",class:"mr-1"}),r(p(v.value.service_dateTime),1)]),_:1})])):(f(),R("span",Pt,[r(": "),e(w,{color:"warning",size:"small"},{default:s(()=>[e(i,{icon:"ri-close-line",class:"mr-1"}),r("Not Set")]),_:1})]))]),t("div",Rt,[At,v.value.status=="Approved"?(f(),R("span",zt,[r(": "),e(w,{color:"warning",size:"small"},{default:s(()=>[e(i,{icon:"ri-check-double-fill",class:"mr-1"}),r(p(v.value.duration),1)]),_:1})])):(f(),R("span",Et,[r(": "),e(w,{color:"warning",size:"small"},{default:s(()=>[e(i,{icon:"ri-close-line",class:"mr-1"}),r("Not Set")]),_:1})]))]),t("div",jt,[Ft,v.value.status=="Approved"?(f(),R("span",Ut,[r(": "),e(w,{color:"warning",size:"small"},{default:s(()=>[e(i,{icon:"ri-check-double-fill",class:"mr-1"}),r(" ~ RM"+p(v.value.approximated_price),1)]),_:1})])):(f(),R("span",Ht,[r(": "),e(w,{color:"warning",size:"small"},{default:s(()=>[e(i,{icon:"ri-close-line",class:"mr-1"}),r("Not Set")]),_:1})]))]),t("div",Nt,[Yt,t("span",null,": "+p(v.value.place_to_service),1)]),e(ie,{class:"mt-2 mb-2"}),t("div",qt,[Bt,t("span",Lt,": "+p(v.value.remark_customer),1)]),t("div",Wt,[Qt,t("span",Gt,": "+p(v.value.remark_provider),1)])]),_:1}),e(J,{class:"pt-5 mt-4"},{default:s(()=>[e(Z,null,{default:s(()=>[e(z,{cols:"12",md:"4"},{default:s(()=>[e(Y,{color:"secondary",onClick:$[0]||($[0]=u=>E.value=!1)},{default:s(()=>[e(i,{icon:"ri-close-line",class:"mr-1"}),r(" Close ")]),_:1})]),_:1}),e(z,{cols:"12",md:"4"}),e(z,{cols:"12",md:"4"},{default:s(()=>[v.value.status=="Approved"?(f(),O(Y,{key:0,color:"success",onClick:$[1]||($[1]=u=>I(v.value.user))},{default:s(()=>[e(i,{icon:"ri-whatsapp-line",class:"mr-1"}),e(ue,{location:"top",activator:"parent",transition:"scroll-x-transition"},{default:s(()=>[r(" Contact Buyer ")]),_:1}),r(" Contact ")]),_:1})):N("",!0)]),_:1})]),_:1})]),_:1})]),_:1})]),_:1},8,["modelValue"]),e(ne,{modelValue:h.value,"onUpdate:modelValue":$[3]||($[3]=u=>h.value=u),scrollable:"","max-width":"500"},{default:s(()=>[e(De,{clickedUser:T.value},null,8,["clickedUser"])]),_:1},8,["modelValue"]),e(pe,{modelValue:g.value,"onUpdate:modelValue":$[4]||($[4]=u=>g.value=u),location:"top end",transition:"scale-transition",color:"success"},{default:s(()=>[e(i,{size:"20",class:"me-2"},{default:s(()=>[r("ri-checkbox-circle-line")]),_:1}),r(" Order has been successfully approved. ")]),_:1},8,["modelValue"])],64))}},Kt=me(Jt,[["__scopeId","data-v-ec2f57d6"]]),H=C=>(_e("data-v-30a1ad4d"),C=C(),fe(),C),Xt={class:"text-uppercase text-center"},Zt={class:"text-uppercase text-center"},es={class:"text-uppercase text-center"},ts={class:"text-uppercase text-center"},ss={class:"text-uppercase text-center"},as={class:"text-uppercase text-center"},ls={class:"text-uppercase text-center"},rs={class:"text-center"},ns={class:"d-flex align-items-center"},os={style:{color:"#6c757d","font-size":"12px"}},is={class:"d-flex align-items-center"},ds={style:{color:"#6c757d","font-size":"12px"}},cs={class:"text-center"},us={class:"text-center"},ps={class:"text-center"},ms={class:"status-dot"},_s={class:"text-center"},fs=H(()=>t("span",null,"Confirm Order",-1)),vs={class:"text-uppercase text-center"},hs={class:"text-uppercase text-center"},gs={class:"text-uppercase text-center"},xs={class:"text-uppercase text-center"},Ss={class:"text-uppercase text-center"},ys={class:"text-uppercase text-center"},$s={colspan:"6",class:"text-center"},bs=H(()=>t("h3",{class:"mt-2"},"Order Information",-1)),Vs={class:"detail-row"},Ds=H(()=>t("strong",null,"Order at",-1)),ks={class:"detail-row"},Os=H(()=>t("strong",null,"Place to service",-1)),Cs={class:"detail-row",style:{display:"flex","align-items":"flex-start"}},Ts=H(()=>t("strong",null,"Remark customer",-1)),Is={style:{"white-space":"pre-line"}},ws={class:"detail-row"},Ms=H(()=>t("strong",null,"Service duration",-1)),Ps={class:"detail-row"},Rs=H(()=>t("strong",null,"Approximated earn",-1)),As=H(()=>t("h3",{class:"mt-6"},"Customer Information",-1)),zs={style:{display:"inline-block","vertical-align":"top"}},Es={style:{color:"#6c757d","font-size":"12px"}},js=H(()=>t("h3",null,"Service Information",-1)),Fs=H(()=>t("label",{for:"firstNameHorizontalIcons"},"Service Date and Time",-1)),Us=H(()=>t("label",{for:"remarkHorizontalIcons"},"Remark",-1)),Hs={__name:"OrderServicePendingTable",props:{pendingOrders:{type:Object,required:!0},getPendingOrders:{type:Function,required:!0}},emits:["update:pendingOrders"],setup(C,{emit:v}){const E=C,g=y({}),h=y(!1),T=y(!1),D=y(""),I=y(""),j=y(""),M=y(!1),$=y(!1),u=o=>{h.value=!0,g.value=o,G([o]),console.log(g.value)},Q=()=>{j.value="approve",T.value=!0},q=()=>{j.value="reject",T.value=!0},G=o=>{const l=o[0].remark_customer,a=l.split("• ")[1].split(",")[0].trim(),n=l.split("• ")[1].split(",")[1].split("-")[0].trim(),c=l.split("• ")[1].split(",")[1].split("-")[1].trim().split(`
`)[0];D.value=`${a}, ${n}-${c}`},B=o=>{const l=o.split(",")[0].trim(),a=o.split(",")[1].split("-")[0].trim(),n=o.split(",")[1].split("-")[1].trim(),c=l.split("/")[0],x=l.split("/")[1],_=l.split("/")[2],k=a.split(" ")[1]==="AM"?parseInt(a.split(":")[0]):parseInt(a.split(":")[0])+12,b=parseInt(a.split(":")[1]),S=n.split(" ")[1]==="AM"?parseInt(n.split(":")[0]):parseInt(n.split(":")[0])+12,V=parseInt(n.split(":")[1]);return`${c}-${x}-${_} ${String(k).padStart(2,"0")}:${String(b).padStart(2,"0")}-${String(S).padStart(2,"0")}:${String(V).padStart(2,"0")}`},d=()=>{if(j.value==="approve")D.value=B(D.value);else if(D.value=null,I.value==="")return;const o={service_dateTime:D.value,remark_provider:I.value};ae.put("/api/order-services/confirmed-order/"+g.value.id,o).then(l=>{T.value=!1,h.value=!1,D.value=null,I.value="",j.value==="approve"?M.value=!0:$.value=!0,j.value="",E.getPendingOrders()}).catch(l=>{console.log(l)})};return(o,l)=>(f(),R(X,null,[C.pendingOrders.length>0?(f(),O(re,{key:0,height:"250","fixed-header":""},{default:s(()=>[t("thead",null,[t("tr",null,[t("th",Xt,[e(i,{icon:"ri-info-i"}),r(" Order ID ")]),t("th",Zt,[e(i,{icon:"ri-product-hunt-line"}),r(" Service ")]),t("th",es,[e(i,{icon:"ri-id-card-line"}),r(" Customer ")]),t("th",ts,[e(i,{icon:"ri-hourglass-fill"}),r(" Service Duration ")]),t("th",ss,[e(i,{icon:"ri-calendar-schedule-line"}),r(" Order Date ")]),t("th",as,[e(i,{icon:"ri-verified-badge-line"}),r(" Status ")]),t("th",ls,[e(i,{icon:"ri-eye-line"}),r(" View ")])])]),t("tbody",null,[(f(!0),R(X,null,se(C.pendingOrders,a=>(f(),R("tr",{key:a.id},[t("td",rs,[e(w,{color:"primary"},{default:s(()=>[r(p(a.id),1)]),_:2},1024)]),t("td",null,[t("div",ns,[a.service.pictures.length>0&&a.service.pictures?(f(),O(te,{key:0,size:"32",rounded:"sm",style:{"margin-inline-end":"8px"}},{default:s(()=>[e(K,{src:a.service.pictures[0].picture_path},null,8,["src"])]),_:2},1024)):N("",!0),t("div",null,[t("div",null,p(a.service.name),1),t("div",os,p(a.service.type),1)])])]),t("td",null,[t("div",is,[a.user.avatar?(f(),O(te,{key:0,size:"32",color:a.user.avatar?"":"primary",class:"mr-1"},{default:s(()=>[e(K,{src:a.user.avatar},null,8,["src"])]),_:2},1032,["color"])):N("",!0),t("div",null,[t("div",null,p(a.user.username),1),t("div",ds,p(a.user.email),1)])])]),t("td",cs,p(a.duration),1),t("td",us,p(a.order_dateTime),1),t("td",ps,[t("div",ms,[e(w,{color:"warning"},{default:s(()=>[r(p(a.status),1)]),_:2},1024)])]),t("td",_s,[e(Y,{color:"warning",style:{"margin-inline":"15px 3px"},size:"small",onClick:n=>u(a)},{default:s(()=>[e(i,{icon:"ri-eye-line"}),e(ue,{"open-delay":"500",location:"top",activator:"parent",transition:"scroll-y-transition"},{default:s(()=>[fs]),_:1})]),_:2},1032,["onClick"])])]))),128))])]),_:1})):(f(),O(re,{key:1,height:"140","fixed-header":""},{default:s(()=>[t("thead",null,[t("tr",null,[t("th",vs,[e(i,{icon:"ri-info-i"}),r(" Order ID ")]),t("th",hs,[e(i,{icon:"ri-product-hunt-line"}),r(" Service ")]),t("th",gs,[e(i,{icon:"ri-id-card-line"}),r(" Customer ")]),t("th",xs,[e(i,{icon:"ri-calendar-schedule-line"}),r(" Order Date ")]),t("th",Ss,[e(i,{icon:"ri-verified-badge-line"}),r(" Status ")]),t("th",ys,[e(i,{icon:"ri-eye-line"}),r(" View ")])])]),t("tbody",null,[t("tr",null,[t("td",$s,[e(ve,{variant:"tonal",type:"warning",class:"mt-2",color:"primary",closable:"",dense:""},{default:s(()=>[r(" No pending orders found. ")]),_:1})])])])]),_:1})),e(ne,{modelValue:m(h),"onUpdate:modelValue":l[0]||(l[0]=a=>F(h)?h.value=a:null),scrollable:"","max-width":"800"},{default:s(()=>[e(ce,null,{default:s(()=>[e(Me,{class:"pb-3"},{default:s(()=>[bs]),_:1}),e(J,null,{default:s(()=>[t("div",Vs,[Ds,t("span",null,": "+p(m(g).order_dateTime),1)]),t("div",ks,[Os,t("span",null,": "+p(m(g).place_to_service),1)]),t("div",Cs,[Ts,t("span",Is,": "+p(m(g).remark_customer),1)]),t("div",ws,[Ms,t("span",null,[r(": "),e(w,{color:"primary",class:"mr-2",size:"small"},{default:s(()=>[e(i,{icon:"ri-time-fill",class:"mr-2"}),r(" "+p(m(g).duration),1)]),_:1})])]),t("div",Ps,[Rs,t("span",null,[r(": "),e(w,{color:"primary",class:"mr-2",size:"small"},{default:s(()=>[e(i,{icon:"ri-money-dollar-circle-line",class:"mr-2"}),r(" ~ RM "+p(m(g).approximated_price),1)]),_:1})])]),As,e(J,{class:"font-weight-medium text-high-emphasis text-truncate",style:{display:"flex","align-items":"center"}},{default:s(()=>[e(te,{size:"40",color:m(g).user.avatar?"":"primary",class:be(`${m(g).user.avatar?"":"v-avatar-light-bg primary--text"}`),variant:m(g).user.avatar?void 0:"tonal",style:{"margin-inline-end":"8px"}},{default:s(()=>[e(K,{src:m(g).user.avatar},null,8,["src"])]),_:1},8,["color","class","variant"]),t("div",zs,[t("div",null,p(m(g).user.username),1),t("div",Es,p(m(g).user.email),1)])]),_:1}),js,e(Ae,{"delimiter-icon":"ri-circle-fill",height:"250px","hide-delimiter-background":"","show-arrows":"hover"},{default:s(()=>[(f(!0),R(X,null,se(m(g).service.pictures,(a,n)=>(f(),O(ze,{key:n},{default:s(()=>[e(K,{src:a.picture_path,height:"250px","object-fit":"cover",class:"mt-2 ml-2 mr-2"},null,8,["src"])]),_:2},1024))),128))]),_:1}),e(le,null,{default:s(()=>[e(i,{icon:"ri-shopping-bag-4-line"}),r(" "+p(m(g).service.name),1)]),_:1}),e(le,null,{default:s(()=>[e(i,{icon:"ri-money-dollar-circle-line"}),r(" RM "+p(m(g).service.price_per_hour)+" /hour",1)]),_:1})]),_:1}),e(J,{class:"pt-5 d-flex mt-4 justify-content-center",style:{"margin-inline-start":"300px"}},{default:s(()=>[e(Y,{color:"error",class:"me-4",onClick:q},{default:s(()=>[r(" Reject ")]),_:1}),e(Y,{color:"success",onClick:Q},{default:s(()=>[r(" Approve ")]),_:1})]),_:1})]),_:1})]),_:1},8,["modelValue"]),e(ne,{modelValue:m(T),"onUpdate:modelValue":l[7]||(l[7]=a=>F(T)?T.value=a:null),"max-width":"600"},{default:s(()=>[e(ce,{title:"Confirm Order Information"},{default:s(()=>[e(J,null,{default:s(()=>[e(Re,{ref:"refForm",onSubmit:l[6]||(l[6]=ke(()=>{},["prevent"]))},{default:s(()=>[e(Z,null,{default:s(()=>[m(j)==="approve"?(f(),O(z,{key:0,cols:"12"},{default:s(()=>[e(Z,{"no-gutters":"",style:{cursor:"not-allowed"}},{default:s(()=>[e(z,{cols:"12",md:"3"},{default:s(()=>[Fs]),_:1}),e(z,{cols:"12",md:"9"},{default:s(()=>[e(Ve,{modelValue:m(D),"onUpdate:modelValue":l[1]||(l[1]=a=>F(D)?D.value=a:null),"prepend-inner-icon":"ri-calendar-event-line",disabled:"",rules:[m(xe)]},null,8,["modelValue","rules"])]),_:1})]),_:1})]),_:1})):N("",!0),e(z,{cols:"12"},{default:s(()=>[e(Z,{"no-gutters":""},{default:s(()=>[e(z,{cols:"12",md:"3"},{default:s(()=>[Us]),_:1}),e(z,{cols:"12",md:"9"},{default:s(()=>[m(j)==="reject"?(f(),O(Se,{key:0,modelValue:m(I),"onUpdate:modelValue":l[2]||(l[2]=a=>F(I)?I.value=a:null),"prepend-inner-icon":"ri-chat-4-line",placeholder:"Enter remark for customer (Reason for Rejection)",rules:[m(xe)]},null,8,["modelValue","rules"])):(f(),O(Se,{key:1,modelValue:m(I),"onUpdate:modelValue":l[3]||(l[3]=a=>F(I)?I.value=a:null),"prepend-inner-icon":"ri-chat-4-line",placeholder:"Enter remark for customer (optional)"},null,8,["modelValue"]))]),_:1})]),_:1})]),_:1}),e(z,{"offset-md":"3",cols:"12",md:"9",class:"d-flex gap-4"},{default:s(()=>[e(Y,{class:"ml-8",color:"secondary",onClick:l[4]||(l[4]=a=>T.value=!1),variant:"tonal"},{default:s(()=>[r(" Close ")]),_:1}),e(Y,{type:"submit",onClick:l[5]||(l[5]=a=>o.$refs.refForm.validate().then(()=>d()))},{default:s(()=>[r(" Send ")]),_:1})]),_:1})]),_:1})]),_:1},512)]),_:1})]),_:1})]),_:1},8,["modelValue"]),e(pe,{modelValue:m(M),"onUpdate:modelValue":l[8]||(l[8]=a=>F(M)?M.value=a:null),location:"top end",transition:"scale-transition",color:"success"},{default:s(()=>[e(i,{size:"20",class:"me-2"},{default:s(()=>[r("ri-checkbox-circle-line")]),_:1}),r(" Order has been successfully approved. ")]),_:1},8,["modelValue"]),e(pe,{modelValue:m($),"onUpdate:modelValue":l[9]||(l[9]=a=>F($)?$.value=a:null),location:"top end",transition:"scale-transition",color:"success"},{default:s(()=>[e(i,{size:"20",class:"me-2"},{default:s(()=>[r("ri-checkbox-circle-line")]),_:1}),r(" Order has been successfully rejected. ")]),_:1},8,["modelValue"])],64))}},Ns=me(Hs,[["__scopeId","data-v-30a1ad4d"]]),Ys={style:{display:"flex","align-items":"center"}},qs={__name:"export-service",props:{passStatus:{type:Object,default:null},passOrderDateRange:{type:Array,default:null},passServiceDateRange:{type:Array,default:null},passSearch:{type:String,default:null}},setup(C){const v=C,{passOrderDateRange:E}=he(v),{passServiceDateRange:g}=he(v),h=y([]),T=y([{id:1,title:"PDF",value:"PDF",icon:"ri-file-pdf-line"},{id:2,title:"Excel",value:"Excel",icon:"ri-file-excel-2-line"},{id:3,title:"CSV",value:"CSV",icon:"ri-file-text-line"},{id:4,title:"Print",value:"Print",icon:"ri-printer-line"}]),D=y(null),I=Ee,j=()=>{const d=new Fe,o=h.value.map(_=>[_.id,_.order_dateTime,_.user.username,_.service.name,_.service_dateTime,_.place_to_service,_.duration,_.approximated_price,_.rating,_.status]),l=new Date,a=`${l.getFullYear()}/${l.getMonth()+1}/${l.getDate()} ${l.getHours()}:${("0"+l.getMinutes()).slice(-2)}:${("0"+l.getSeconds()).slice(-2)}`;let n=d.internal.pageSize.getWidth(),c=30,x=n/2-c/2;d.addImage(I,"PNG",x,12,c,c),d.setFontSize(18),d.text("Report of Sales Services",14,50),d.setFontSize(10),d.setTextColor(128),d.text(`Export Date: ${a}`,14,58),d.autoTable({theme:"grid",head:[["Order ID","Order Date","Customer","Service","Service Date","Place to Service","Duration","Approximated Price","Rating","Status"]],body:o,styles:{fillColor:[255,255,255],textColor:20,fontSize:10},headStyles:{fillColor:[93,123,234],textColor:255,fontSize:10},margin:{top:65},startY:65}),d.save("SalesServices.pdf")},M=()=>{const d=h.value.map(a=>({"Order ID":a.id,"Order Date":a.order_dateTime,Customer:a.user.username,Service:a.service.name,"Service Date":a.service_dateTime,"Place to Service":a.place_to_service,Duration:a.duration,"Approximated Price":a.approximated_price,Rating:a.rating,Status:a.status})),o=de.json_to_sheet(d),l=de.book_new();de.book_append_sheet(l,o,"Sheet1"),Ue(l,"SalesServices.xlsx")},$=()=>{const d=h.value.map(c=>({"Order ID":c.id,"Order Date":c.order_dateTime,Customer:c.user.username,Service:c.service.name,"Service Date":c.service_dateTime,"Place to Service":c.place_to_service,Duration:c.duration,"Approximated Price":c.approximated_price,Rating:c.rating,Status:c.status})),o=He.unparse(d),l=new Blob([o],{type:"text/csv;charset=utf-8"}),a=URL.createObjectURL(l),n=document.createElement("a");n.href=a,n.download="SalesServices.csv",document.body.appendChild(n),n.click(),document.body.removeChild(n)},u=Oe(()=>{const d=h.value.map(a=>({"Order ID":a.id,"Order Date":a.order_dateTime,Customer:a.user.username,Service:a.service.name,"Service Date":a.service_dateTime,"Place to Service":a.place_to_service,Duration:a.duration,"Approximated Price":a.approximated_price,Rating:a.rating,Status:a.status})),o=`<tr>${Object.keys(d[0]).map(a=>`<th style="text-align: left">${a}</th>`).join("")}</tr>`,l=d.map(a=>`<tr>${Object.values(a).map(n=>`<td>${n}</td>`).join("")}</tr>`).join("");return`<table><thead>${o}</thead><tbody>${l}</tbody></table>`}),Q=()=>{const d=window.open("","","height=400,width=800");d.document.write(`<html><head><style>table {border-collapse: collapse; widh: 100%} td,th {border: 1px solid #dddddd; text align:left; padding: 8px;} th {background-color: #f2f2f2;}</style></head><body>${u.value}</body></html>`),d.document.close(),d.focus(),d.print(),d.close()},q=d=>{const o=d.getDate().toString().padStart(2,"0"),l=(d.getMonth()+1).toString().padStart(2,"0"),a=d.getFullYear();return`${o}-${l}-${a}`},G=je(()=>{let d="/api/order-services/get-confirmed-sell-orders";const o=(a,n)=>{n&&(d+=d.includes("?")?`&${a}=${n}`:`?${a}=${n}`)};o("search",v.passSearch&&v.passSearch.length>2?v.passSearch:null),o("status",v.passStatus.value||null);const l=(a,n)=>{if(!n)return;const c=q(n[0]),x=q(n[1]);o(a,`${c} - ${x}`)};l("order_date",E.value),l("service_date",g.value),ae.get(d).then(({data:a})=>{h.value=a.data,h.value.forEach(n=>{const c=x=>{const _=new Date(x),k=_.getFullYear(),b=String(_.getMonth()+1).padStart(2,"0"),S=String(_.getDate()).padStart(2,"0");let V=_.getHours();const A=String(_.getMinutes()).padStart(2,"0"),P=V>=12?"pm":"am";return V=V%12||12,`${k}-${b}-${S} ${V}:${A} ${P}`};n.order_dateTime=c(n.order_dateTime)}),h.value.forEach(n=>{if(n.service_dateTime){const c=n.service_dateTime.split(" "),x=c[1].split("-")[0],_=c[1].split("-")[1],k=x.split(":"),b=_.split(":");let S=parseInt(k[0]),V=parseInt(b[0]),A="AM",P="AM";S>=12&&(S-=12,A="PM"),V>=12&&(V-=12,P="PM");const L=`${c[0]} ${S}:${k[1]} ${A}-${V}:${b[1]} ${P}`;n.service_dateTime=L}}),h.value.forEach(n=>{n.service.pictures.forEach(c=>{c.picture_path="http://127.0.0.1:8000/storage/"+c.picture_path})}),h.value.forEach(n=>{const c=(b,S)=>{const V=b.split(" ")[1]==="AM"?parseInt(b.split(":")[0]):parseInt(b.split(":")[0])+12,A=S.split(" ")[1]==="AM"?parseInt(S.split(":")[0]):parseInt(S.split(":")[0])+12,P=parseInt(b.split(":")[1]),L=parseInt(S.split(":")[1]),ee=String(A-V).padStart(2,"0"),W=String(L-P).padStart(2,"0");return W<0?`${String(parseInt(ee)-1).padStart(2,"0")}:${String(parseInt(W)+60).padStart(2,"0")}`:`${ee}:${W}`},x=n.remark_customer,_=x.split("• ")[1].split(",")[1].split("-")[0].trim(),k=x.split("• ")[1].split(",")[1].split("-")[1].trim().split(`
`)[0];n.duration=c(_,k)}),h.value.forEach(n=>{(n.status==="Rejected"||n.status==="Cancelled")&&(n.service_dateTime="null")}),h.value.forEach(n=>{n.rating===null&&(n.rating="null")}),D.value==="Print"?Q():D.value==="PDF"?j():D.value==="Excel"?M():D.value==="CSV"&&$()}).catch(a=>{console.log(a)})},800),B=d=>{D.value=d,G()};return(d,o)=>(f(),O(Ne,{"open-on-hover":""},{activator:s(({props:l})=>[e(Y,Ce(l,{"prepend-icon":"ri-download-2-line"}),{default:s(()=>[r(" Export ")]),_:2},1040)]),default:s(()=>[e(Ie,null,{default:s(()=>[(f(!0),R(X,null,se(T.value,l=>(f(),O(we,{key:l.value,onClick:a=>B(l.value)},{default:s(()=>[t("div",Ys,[e(i,{icon:l.icon,size:"16px",style:{"margin-inline-end":"5px"}},null,8,["icon"]),r(" "+p(l.title),1)])]),_:2},1032,["onClick"]))),128))]),_:1})]),_:1}))}},oe=C=>(_e("data-v-58d84114"),C=C(),fe(),C),Bs={class:"box-style"},Ls=oe(()=>t("span",null,"Order of Services",-1)),Ws=oe(()=>t("p",null,[r(" This page displays pending and confirmed orders. Click ‘View’ to see order details. "),t("strong",null,"Grey color Order ID indicates orders past their service_dateTime for Confirmed Orders.")],-1)),Qs=oe(()=>t("span",null,"Pending Order",-1)),Gs=oe(()=>t("span",null,"Confirmed Order",-1)),Js={class:"table-style"},Ks={class:"table-style"},Xs={__name:"sales-services",setup(C){const v=y("tab-1"),E=y([]),g=y([]),h=y(""),T=y([]),D=y(null),I=y(null),j=y(null),M=y(null),$=y(null),u=y(null),Q=[{name:"Approved",value:"Approved"},{name:"Rejected",value:"Rejected"},{name:"Cancelled",value:"Cancelled"}],q=()=>{ae.get("/api/order-services/get-pending-sell-orders").then(d=>{E.value=d.data.data,E.value.forEach(o=>{const l=a=>{const n=new Date(a),c=n.getFullYear(),x=String(n.getMonth()+1).padStart(2,"0"),_=String(n.getDate()).padStart(2,"0");let k=n.getHours();const b=String(n.getMinutes()).padStart(2,"0"),S=k>=12?"pm":"am";return k=k%12||12,`${c}-${x}-${_} ${k}:${b} ${S}`};o.order_dateTime=l(o.order_dateTime),o.service_dateTime=l(o.service_dateTime)}),E.value.forEach(o=>{o.service.pictures.forEach(l=>{l.picture_path="http://127.0.0.1:8000/storage/"+l.picture_path})}),E.value.forEach(o=>{const l=(x,_)=>{const k=x.split(" ")[1]==="AM"?parseInt(x.split(":")[0]):parseInt(x.split(":")[0])+12,b=_.split(" ")[1]==="AM"?parseInt(_.split(":")[0]):parseInt(_.split(":")[0])+12,S=parseInt(x.split(":")[1]),V=parseInt(_.split(":")[1]),A=String(b-k).padStart(2,"0"),P=String(V-S).padStart(2,"0");return P<0?`${String(parseInt(A)-1).padStart(2,"0")}:${String(parseInt(P)+60).padStart(2,"0")}`:`${A}:${P}`},a=o.remark_customer,n=a.split("• ")[1].split(",")[1].split("-")[0].trim(),c=a.split("• ")[1].split(",")[1].split("-")[1].trim().split(`
`)[0];o.duration=l(n,c)})}).catch(d=>{console.log(d)})},G=d=>{const o=d.getDate().toString().padStart(2,"0"),l=(d.getMonth()+1).toString().padStart(2,"0"),a=d.getFullYear();return`${o}-${l}-${a}`},B=Ye.debounce(()=>{let d="/api/order-services/get-confirmed-sell-orders";const o=(a,n)=>{n&&(d+=d.includes("?")?`&${a}=${n}`:`?${a}=${n}`)};o("search",h.value&&h.value.length>2?h.value:null),o("status",T.value.value||null);const l=(a,n)=>{if(!n)return;const c=G(n[0]),x=G(n[1]);o(a,`${c} - ${x}`)};l("order_date",D.value),l("service_date",I.value),ae.get(d).then(({data:a})=>{g.value=a.data,g.value.forEach(n=>{const c=x=>{const _=new Date(x),k=_.getFullYear(),b=String(_.getMonth()+1).padStart(2,"0"),S=String(_.getDate()).padStart(2,"0");let V=_.getHours();const A=String(_.getMinutes()).padStart(2,"0"),P=V>=12?"pm":"am";return V=V%12||12,`${k}-${b}-${S} ${V}:${A} ${P}`};n.order_dateTime=c(n.order_dateTime)}),g.value.forEach(n=>{if(n.service_dateTime){const c=n.service_dateTime.split(" "),x=c[1].split("-")[0],_=c[1].split("-")[1],k=x.split(":"),b=_.split(":");let S=parseInt(k[0]),V=parseInt(b[0]),A="AM",P="AM";S>=12&&(S-=12,A="PM"),V>=12&&(V-=12,P="PM");const L=`${c[0]} ${S}:${k[1]} ${A}-${V}:${b[1]} ${P}`;n.service_dateTime=L}}),g.value.forEach(n=>{n.service.pictures.forEach(c=>{c.picture_path="http://127.0.0.1:8000/storage/"+c.picture_path})}),g.value.forEach(n=>{const c=(b,S)=>{const V=b.split(" ")[1]==="AM"?parseInt(b.split(":")[0]):parseInt(b.split(":")[0])+12,A=S.split(" ")[1]==="AM"?parseInt(S.split(":")[0]):parseInt(S.split(":")[0])+12,P=parseInt(b.split(":")[1]),L=parseInt(S.split(":")[1]),ee=String(A-V).padStart(2,"0"),W=String(L-P).padStart(2,"0");return W<0?`${String(parseInt(ee)-1).padStart(2,"0")}:${String(parseInt(W)+60).padStart(2,"0")}`:`${ee}:${W}`},x=n.remark_customer,_=x.split("• ")[1].split(",")[1].split("-")[0].trim(),k=x.split("• ")[1].split(",")[1].split("-")[1].trim().split(`
`)[0];n.duration=c(_,k)})}).catch(a=>{console.log(a)})},800);return Te([h,T,D,I,v],()=>{B(),j.value=T.value,M.value=D.value,$.value=I.value,u.value=h.value}),B(),q(),(d,o)=>(f(),R("div",Bs,[e(ve,{variant:"tonal",color:"info"},{title:s(()=>[e(i,{icon:"ri-information-line",class:"mr-2"}),Ls]),default:s(()=>[Ws]),_:1}),e(qe,{modelValue:m(v),"onUpdate:modelValue":o[0]||(o[0]=l=>F(v)?v.value=l:null),grow:"",stacked:""},{default:s(()=>[e(ye,{value:"tab-1"},{default:s(()=>[e(i,{icon:"ri-loader-line",class:"mb-2"}),Qs]),_:1}),e(ye,{value:"tab-2"},{default:s(()=>[e(i,{icon:"ri-takeaway-line",class:"mb-2"}),Gs]),_:1})]),_:1},8,["modelValue"]),e(Be,{modelValue:m(v),"onUpdate:modelValue":o[6]||(o[6]=l=>F(v)?v.value=l:null),class:"mt-5"},{default:s(()=>[e($e,{value:"tab-1"},{default:s(()=>[t("div",Js,[e(Ns,{pendingOrders:m(E),getPendingOrders:q},null,8,["pendingOrders"])])]),_:1}),e($e,{value:"tab-2"},{default:s(()=>[e(le,{class:"ml-2 mr-2 mt-1"},{default:s(()=>[r(" Filter Confirmed Orders")]),_:1}),e(Pe,{class:"ml-2 mr-2 mb-2"},{default:s(()=>[r(" Filter confirmed orders by order status, order date, service date and search for customer and service.")]),_:1}),e(Z,{class:"ml-2 mr-2"},{default:s(()=>[e(z,{cols:"12",md:"4"},{default:s(()=>[e(Le,{modelValue:m(T),"onUpdate:modelValue":o[1]||(o[1]=l=>F(T)?T.value=l:null),chips:"","return-object":"","item-title":"name",items:Q,"prepend-inner-icon":"ri-filter-3-line",placeholder:"Select Order Status",clearable:"",density:"compact","onClick:clear":o[2]||(o[2]=l=>T.value=[])},null,8,["modelValue"])]),_:1}),e(z,{cols:"12",md:"4"},{default:s(()=>[e(m(ge),{"teleport-center":"",clearable:!0,modelValue:m(D),"onUpdate:modelValue":o[3]||(o[3]=l=>F(D)?D.value=l:null),placeholder:"Select Date Range (Order Date)","enable-time-picker":!1,format:"dd/MM/yyyy",range:"",class:"mt-2"},null,8,["modelValue"])]),_:1}),e(z,{cols:"12",md:"4"},{default:s(()=>[e(m(ge),{"teleport-center":"",clearable:!0,modelValue:m(I),"onUpdate:modelValue":o[4]||(o[4]=l=>F(I)?I.value=l:null),placeholder:"Select Date Range (Meet Date)","enable-time-picker":!1,format:"dd/MM/yyyy",range:"",class:"mt-2"},null,8,["modelValue"])]),_:1})]),_:1}),e(Z,{class:"ml-2 mr-2"},{default:s(()=>[e(z,{cols:"12",md:"4"},{default:s(()=>[e(qs,{passStatus:m(j),passOrderDateRange:m(M),passServiceDateRange:m($),passSearch:m(u)},null,8,["passStatus","passOrderDateRange","passServiceDateRange","passSearch"])]),_:1}),e(z,{cols:"12",md:"4"}),e(z,{cols:"12",md:"4"},{default:s(()=>[e(Ve,{class:"mb-4",modelValue:m(h),"onUpdate:modelValue":o[5]||(o[5]=l=>F(h)?h.value=l:null),label:"Search Customer and Service",density:"compact",placeholder:"Search for customer and service",dense:"",clearable:""},null,8,["modelValue"])]),_:1})]),_:1}),t("div",Ks,[e(Kt,{confirmedOrders:m(g),getConfirmedOrders:m(B)},null,8,["confirmedOrders","getConfirmedOrders"])])]),_:1})]),_:1},8,["modelValue"])]))}},Oa=me(Xs,[["__scopeId","data-v-58d84114"]]);export{Oa as default};
