var pre_sex="男\n女";var pre_education="初中\n高中\n大学本科\n硕士研究生\n博士研究生";var pre_age="18岁以下\n18~25\n26~30\n31~40\n41~50\n51~60\n60以上";var pre_satisfaction="很不满意\n不满意\n一般\n满意\n很满意";var pre_agree="很不同意\n不同意\n一般\n同意\n很同意";var pre_accord="很不符合\n不符合\n一般\n符合\n很符合";var pre_num="1\n2\n3\n4\n5";var pre_week="星期日\n星期一\n星期二\n星期三\n星期四\n星期五\n星期六";var pre_month="1月份\n2月份\n3月份\n4月份\n5月份\n6月份\n7月份\n8月份\n9月份\n10月份\n11月份\n12月份";var pre_year="1980\n1981\n1982\n1983\n1984\n1985\n1986\n1987\n1988\n1989\n1990\n1991\n1992\n1993\n1994\n1995\n1996\n1997\n1998\n1999\n2000\n2001\n2002\n2003\n2004\n2005\n2006\n2007\n2008\n2009\n2010\n2011\n2012\n2013\n2014\n2015";var pre_yesorno="是\n否";var per_province="安徽\n北京\n重庆\n福建\n甘肃\n广东\n广西\n贵州\n海南\n河北\n黑龙江\n河南\n香港\n湖北\n湖南\n江苏\n江西\n吉林\n辽宁\n澳门\n内蒙古\n宁夏\n青海\n山东\n上海\n山西\n陕西\n四川\n台湾\n天津\n新疆\n西藏\n云南\n浙江\n海外";function setFreOptionPreview(a){var b=document.getElementById("prepop");b.value="";b.value=a;}function confirmSetFreOptions(a){var a=document.getElementById("prepop");window.parent.cur.setFreOptions(a.value);window.parent.PDF_close("","divOption");}function trim(a){if(a&&a.replace){return a.replace(/(^\s*)|(\s*$)/g,"");}else{return a;}}window.onload=function(){prepop.onpaste=function(){setTimeout(function(){var e=prepop.value.split("\n");var c=0;for(var a=0;a<e.length;a++){if(!trim(e[a])){continue;}c++;}if(c==1){e=prepop.value.split("\t");if(e.length>1){var d=e.join("\n");prepop.value=d;}else{e=prepop.value.split(" ");if(e.length>4){var b=true;for(var a=0;a<e.length;a++){if(trim(e[a].length>6)){b=false;}}if(b){var d=e.join("\n");prepop.value=d;}}}}},40);};};