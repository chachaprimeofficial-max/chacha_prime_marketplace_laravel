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
});