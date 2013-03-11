/*
 * @version   $Id: responsive-selectbox.js 4586 2012-10-27 01:50:24Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
((function(){var a={build:function(){var d=document.getElement("ul.gf-menu"),c=document.getElement(".gf-menu-device-container");
if(!d||!c||d.retrieve("roknavmenu:dropdown:select")){return;}d.store("roknavmenu:dropdown:select",true);var b=new Element("select").inject(c,"top");a.getChildren(d,b,0);
a.attachEvent(b);},getChildren:function(c,m,g){var d=c.getChildren(),b,n,p,j,k,h,o;for(var f=0,e=d.length;f<e;f++){p=d[f].getElement(".item");if(!p){continue;
}n=p.getElement("em")||p.getElement("i");b=d[f].getElement("ul");o=d[f].hasClass("active");j=p.get("text").clean();k=n?n.get("text").clean():"";if(j.length!=k.length){j=j.substr(0,(j.length-1)-(k.length-1));
}h=new Element("option",{value:p.get("href"),text:"-".repeat(g)+" "+j}).inject(m);if(o){h.set("selected","selected");}if(d[f].getElement("ul")){a.getChildren(d[f].getElement("ul"),m,g+1);
}}},attachEvent:function(b){b.addEvent("change",function(){window.location.href=this.value;});}};window.addEvent("domready",a.build);if(typeof ResponsiveMenu!="undefined"){ResponsiveMenu.implement({mediaQuery:function(d){var e=document.getElement(".gf-menu"),c=document.getElement(".gf-menu-device-container"),b=this.toggler.retrieve("roknavmenu:slide");
if(!e&&!c){return;}if(d=="(min-width: 768px)"){e.setStyle("display","inherit");this.slide.wrapper.setStyle("display","none");this.toggler.setStyle("display","none");
}else{e.setStyle("display","none");this.slide.wrapper.setStyle("display","inherit");this.toggler.setStyle("display","block");}b.hide();this.toggler.removeClass("active");
}});}})());