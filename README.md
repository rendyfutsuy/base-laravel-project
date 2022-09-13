
  

  

<p  align="center"><a  href="#"><img  src="https://yt3.ggpht.com/VfV_ymLocHwcEWePEwqFGmVAHhv3OpXz3htVDd7MQM7jP6-pABGsfXiQJr339qybHn3wt4SmLVk=w2120-fcrop64=1,00005a57ffffa5a8-k-c0xffffffff-no-nd-rj"></a></p>

  

<p  align="center">
<a  href="https://travis-ci.org/laravel/framework"><img  src="https://travis-ci.org/laravel/framework.svg"  alt="Build Status"></a>
<a  href="https://packagist.org/packages/laravel/framework"><img  src="https://img.shields.io/packagist/dt/laravel/framework"  alt="Total Downloads"></a>
<a  href="https://packagist.org/packages/laravel/framework"><img  src="https://img.shields.io/packagist/v/laravel/framework"  alt="Latest Stable Version"></a>
<a  href="https://packagist.org/packages/laravel/framework"><img  src="https://img.shields.io/packagist/l/laravel/framework"  alt="License"></a>
</p>
  

## Documentation

We also have more detailed documentation in case you want to learn more on how we develop application with Roketin Base Project [documentation](https://), there best practice for development, how we code, how we implement repository pattern and pipeline design pattern and many more..

  

## Setup Project

#### 1. Clone repository

```sh
> git clone git@github.com:rendyfutsuy/base-laravel-project.git
> cd base-laravel-project
```

  

#### 2. Install Dependencies

```sh
> composer install
> yarn install
```

  

#### 3. Setup Environment Files
copy .env from .env.example, copy .env.testing from .env.testing.example, copy phpunit.xml from phpunit.xml.example and generate app key for laravel
```sh
> php artisan setup:environment
```

  

#### 4. Initial Setup 
Prepare database migration and seeder for local database, generate authentification key (**passport**) and finally compile Dependencies for Frontend.
```sh
> php artisan local:initial-setup
```

  

#### 5. Run Test
Run Local Test to assert all code and setup all work without any fail attempt.
```sh
> php artisan local:test
```
in case there an error for `Pint`, we can fix it with this also re-run all test again
```sh
> php artisan local:test --with-code-fix
```

## Contributor

<table>

<tr>

<td  align="center"><a  href="https://github.com/rendyfutsuy"><img  src="https://avatars.githubusercontent.com/u/22336340?s=96&v=4"  style="border-radius:50%;" width="100px;"  alt=""/><br  /><sub><b>Rendy Anggara</b></sub></a><br  /><a  href="#"  title="Owner">⚜</a><a  href="#"  title="Code">💻</a></td>
</tr>

</table>

©2022 Rendy Anggara. ALL RIGHT RESERVED.
