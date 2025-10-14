
 
<?php $__env->startSection('title', 'Información del Sistema - Coffeeology'); ?>
 
<?php $__env->startSection('content'); ?>
<style>
    /* RESET */
    * { margin: 0; padding: 0; box-sizing: border-box; }
 
    body {
      font-family: 'Montserrat', sans-serif;
      display: flex;
      min-height: 100vh;
      background: linear-gradient(135deg, #0D0D0D, #1a1a1a, #0D0D0D);
      color: #FAF9F6;
    }
 
     
    /* CONTENIDO */
    .content {
      flex: 1;
      margin-left: 220px;
      padding: 40px;
      overflow-y: auto;
      width: calc(100% - 220px);
      animation: fadeIn 1.2s ease-in;
    }
 
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
 
    header {
      text-align: center;
      margin-bottom: 50px;
    }
 
    header h1 {
      font-family: 'Playfair Display', serif;
      font-size: 3rem;
      color: #E6B325;
      text-shadow: 0 0 15px rgba(230,179,37,0.5);
      letter-spacing: 1px;
    }
 
    header p {
      font-size: 1.2rem;
      color: #C0C0C0;
      margin-top: 5px;
    }
 
    .section {
      background: #1a1a1a;
      padding: 30px;
      border-radius: 18px;
      margin-bottom: 45px;
      box-shadow: 0 6px 25px rgba(0,0,0,0.7);
      border: 2px solid #4B2E2E;
      transition: transform 0.3s ease;
    }
 
    .section:hover {
      transform: translateY(-5px);
      border-color: #E6B325;
    }
 
    .section h2 {
      font-family: 'Playfair Display', serif;
      font-size: 1.9rem;
      margin-bottom: 15px;
      color: #E6B325;
      text-shadow: 0 0 8px rgba(230,179,37,0.5);
    }
 
    .section p {
      font-family: 'Lora', serif;
      line-height: 1.7;
      color: #FAF9F6;
      font-size: 1.05rem;
    }
 
    /* EQUIPO DE DESARROLLO */
    .team-photo {
      width: 100%;
      max-width: 800px;
      height: 350px;
      background-color: #0D0D0D;
      border: 2px dashed #4B2E2E;
      border-radius: 15px;
      margin: 0 auto 30px auto;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #C0C0C0;
      font-family: 'Lora', serif;
      font-style: italic;
      font-size: 1.1rem;
    }
 
    .team {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 25px;
      margin-top: 20px;
    }
 
    .team-card {
      background: #0D0D0D;
      padding: 25px;
      border-radius: 15px;
      text-align: center;
      font-family: 'Lora', serif;
      font-size: 1.2rem;
      font-weight: bold;
      color: #FAF9F6;
      box-shadow: 0 4px 20px rgba(0,0,0,0.6);
      cursor: pointer;
      transition: transform 0.3s ease, box-shadow 0.3s ease, border 0.3s ease;
      border: 2px solid #4B2E2E;
      position: relative;
      overflow: hidden;
    }
 
    .team-card::after {
      content: "";
      position: absolute;
      top: 0; left: -100%;
      width: 200%; height: 100%;
      background: linear-gradient(120deg, transparent, rgba(230,179,37,0.2), transparent);
      transition: all 0.6s;
    }
 
    .team-card:hover::after { left: 100%; }
 
    .team-card:hover {
      transform: translateY(-7px);
      box-shadow: 0 8px 30px rgba(230,179,37,0.4);
      border-color: #E6B325;
    }
 
    .team-card span {
      display: block;
      margin-top: 8px;
      font-size: 1rem;
      color: #E6B325;
      font-weight: normal;
    }
 
    footer {
      text-align: center;
      padding: 15px;
      margin-top: 40px;
      font-size: 0.9rem;
      color: #C0C0C0;
      border-top: 1px solid #4B2E2E;
    }
 
    @media (max-width: 767px) {
      .menu-toggle { display: flex; }
      .sidebar { left: -100%; width: 85%; }
      .sidebar.active { left: 0; }
      .sidebar-overlay.active { display: block; }
      .content { margin-left: 0; padding-top: 80px; }
    }
</style>
 
<!-- CONTENIDO -->
<div class="content">
<header>
<h1><i class="fa-solid fa-circle-info"></i> Información del Sistema</h1>
</header>
 
  <section class="section">
<h2>COFFEEOLOGY</h2>
<p>
      Sistema de administración inteligente diseñado para 
<strong>automatizar y optimizar</strong> los procesos operativos en cafeterías y restaurantes. 
      Permite gestionar pedidos, inventario, control de mesas, generación de reportes 
      y administración de usuarios con distintos niveles de acceso.
</p>
</section>
 
  <section class="section">
<h2>Soporte Técnico</h2>
<p><strong>Correo:</strong> zleandro067@gmail.com</p>
<p><strong>Teléfono:</strong> +591 70548255</p>
<p><strong>Horario:</strong> Lunes a Viernes, 9:00 - 18:00</p>
</section>
 
  <section class="section">
<h2>Equipo de Desarrollo</h2>
<p><strong>Universidad del Valle - Proyecto de Sistemas I</strong></p>
 
    <div class="team-photo">
      Espacio para foto grupal del equipo
</div>
 
    <div class="team">
<div class="team-card">👨‍💻 Leandro<span>Los Suchas</span></div>
<div class="team-card">👨‍💻 Alfredo<span>Los Suchas</span></div>
<div class="team-card">👨‍💻 Alejandro<span>Los Suchas</span></div>
<div class="team-card">👨‍💻 Daniel<span>Los Suchas</span></div>
</div>
</section>
 
  <footer>
&copy; 2025 Coffeeology - Todos los derechos reservados.
</footer>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\UNIVALLE\6TO SEMESTRE\Proyecto de Sistemas III\Coffeeology\ProyectoIII-A-LosSuchas\resources\views/informacion/index.blade.php ENDPATH**/ ?>