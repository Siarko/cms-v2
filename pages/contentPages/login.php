<?php
/* @var User $user*/
$user = $this->user;
?>

<div class="container">
    <?php if($user->isAnonymous()):?>
    <div class="title">login</div>
    <div class="content">
        <form action="api/user/login" method="post">
            <div class="inputRow">
                <input type="text" placeholder="Login" name="name">
                <i class="icon fa fa-user"></i>
            </div>
            <div class="inputRow">
                <input type="password" placeholder="Hasło" name="pass">
                <i class="icon fas fa-unlock-alt"></i>
            </div>
            <button type="submit" class="fullWidth">Zaloguj</button>
        </form>
    </div>
    <?php else:?>
    <div class="title">zalogowano jako <?=$user->getName()?></div>
    <div class="content">
        <div class="inputRow">
            <a class="button fullWidth" href="<?=$this->homeLink?>">Strona główna</a>
        </div>
        <?php if($user->canAccessLevel(Page::ROOT_PAGE_PERMS)):?>
        <div class="inputRow">
            <a class="button fullWidth" href="<?=$this->linkRootconsole?>">Konsola roota</a>
        </div>
        <?php endif; ?>
        <div class="inputRow">
            <a class="button fullWidth" href="api/user/logout">Wyloguj</a>
        </div>
    </div>
    <?php endif; ?>
</div>