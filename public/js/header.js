document.addEventListener('DOMContentLoaded',function(){
  const body=document.body;
  const drawer=document.querySelector('[data-cp-drawer]');
  const overlay=document.querySelector('[data-cp-overlay]');
  const openButtons=document.querySelectorAll('[data-cp-open-drawer]');
  const closeButton=document.querySelector('[data-cp-close-drawer]');

  function openDrawer(){
    if(!drawer)return;
    drawer.classList.add('is-open');
    overlay?.classList.add('is-open');
    drawer.setAttribute('aria-hidden','false');
    body.style.overflow='hidden';
  }
  function closeDrawer(){
    if(!drawer)return;
    drawer.classList.remove('is-open');
    overlay?.classList.remove('is-open');
    drawer.setAttribute('aria-hidden','true');
    body.style.overflow='';
  }
  openButtons.forEach(btn=>btn.addEventListener('click',openDrawer));
  closeButton?.addEventListener('click',closeDrawer);
  overlay?.addEventListener('click',closeDrawer);
  document.addEventListener('keydown',e=>{if(e.key==='Escape')closeDrawer()});

  document.querySelectorAll('[data-cp-category-trigger]').forEach(trigger=>{
    trigger.addEventListener('click',function(){
      const group=this.closest('[data-cp-category-group]');
      if(!group)return;
      const wasOpen=group.classList.contains('is-open');
      document.querySelectorAll('[data-cp-category-group].is-open').forEach(other=>{
        if(other!==group){
          other.classList.remove('is-open');
          other.querySelector('[data-cp-category-trigger]')?.setAttribute('aria-expanded','false');
        }
      });
      group.classList.toggle('is-open',!wasOpen);
      this.setAttribute('aria-expanded',String(!wasOpen));
    });
  });

  const picker=document.querySelector('[data-cp-category-select]');
  const toggle=picker?.querySelector('[data-cp-category-toggle]');
  const menu=picker?.querySelector('[data-cp-category-menu]');
  const hidden=picker?.querySelector('input[name="category"]');
  const label=picker?.querySelector('.cp-search-category-label');

  function closePicker(){
    menu?.classList.remove('is-open');
    toggle?.setAttribute('aria-expanded','false');
  }
  toggle?.addEventListener('click',function(e){
    e.stopPropagation();
    const open=!menu.classList.contains('is-open');
    menu.classList.toggle('is-open',open);
    toggle.setAttribute('aria-expanded',String(open));
  });
  menu?.querySelectorAll('[data-category-value]').forEach(option=>{
    option.addEventListener('click',function(){
      hidden.value=this.dataset.categoryValue||'';
      label.textContent=this.textContent.trim();
      menu.querySelectorAll('.is-selected').forEach(x=>x.classList.remove('is-selected'));
      this.classList.add('is-selected');
      closePicker();
    });
  });
  document.addEventListener('click',function(e){
    if(picker && !picker.contains(e.target))closePicker();
  });
  document.querySelectorAll('[data-cp-carousel]').forEach(function(carousel){
    const track=carousel.querySelector('.cp-hero-track');
    const slides=carousel.querySelectorAll('.cp-hero-slide');
    const dots=carousel.querySelector('[data-cp-dots]');
    const prev=carousel.querySelector('[data-cp-prev]');
    const next=carousel.querySelector('[data-cp-next]');
    if(!track || slides.length<2)return;
    let index=0,timer=null;
    if(dots){
      slides.forEach(function(_,i){
        const dot=document.createElement('button');
        dot.type='button'; dot.className='cp-hero-dot'+(i===0?' is-active':'');
        dot.setAttribute('aria-label','Show banner '+(i+1));
        dot.addEventListener('click',function(){go(i,true)});
        dots.appendChild(dot);
      });
    }
    function go(nextIndex,manual){
      index=(nextIndex+slides.length)%slides.length;
      track.style.transform='translateX(-'+(index*100)+'%)';
      dots?.querySelectorAll('.cp-hero-dot').forEach((d,i)=>d.classList.toggle('is-active',i===index));
      if(manual)restart();
    }
    function restart(){
      clearInterval(timer);
      timer=setInterval(()=>go(index+1,false),6000);
    }
    prev?.addEventListener('click',()=>go(index-1,true));
    next?.addEventListener('click',()=>go(index+1,true));
    carousel.addEventListener('mouseenter',()=>clearInterval(timer));
    carousel.addEventListener('mouseleave',restart);
    restart();
  });
});