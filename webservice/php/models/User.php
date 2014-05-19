<?php
     class User extends Model {
          //SQL Fields
          protected $name;
          protected $username;
          protected $authkey;
          protected $signature;
          protected $created;
          protected $password;
          //Additional fields
          protected $groups;
          protected $adminGroups;
          protected $adminArticles;
          protected $groupArticles;
          protected $userArticles;
          //SQL Field Getters
          public function name() {return $this->name;
          }
          public function username() { return $this->username;
          }
          public function authkey() { return $this->authkey;
          }
          public function signature() { return $this->signature;
          }
          public function created() { return $this->created;
          }
          public function password() { return $this->password;
          }
          //SQL Field Setters
          public function setName($name) {
               $this->name = $name;
          }
          public function setAuthkey($authkey) {
               $this->authkey = $authkey;
          }
          public function setUsername($username) {
               $this->username = $username;
          }
          public function setSignature($signature) {
               $this->signature = $signature;
          }
          public function setCreated($created) {
               $this->created = $created;
          }
          public function setPassword($password) {
               $this->passoword = $password;
          }
          //Additional Getters
          public function userArticles($idsOnly = 0) {
               $this->checkUserArticles($idsOnly);
               return $this->userArticles;
          }
          public function groupArticles($idsOnly = 0) {
               $this->checkGroupArticles($idsOnly);
               return $this->groupArticles;
          }
          public function adminArticles($idsOnly = 0) {
               $this->checkAdminArticles($idsOnly);
               return $this->adminArticles;
          }
          public function groups($idsOnly = 0) {
               $this->checkGroups($idsOnly);
               return $this->groups;
          }
          public function adminGroups($idsOnly = 0) {
               $this->checkAdminGroups($idsOnly);
               return $this->adminGroups;
          }
          //Additional Setters
          public function setUserArticles($userarticles) {
               $this->userArticles = $userArticles;
          }
          public function setGroupArticles($grouparticles) {
               $this->groupArticles = $grouparticles;
          }
          public function setAdminArticles($adminArticles) {
               $this->adminArticles = $adminArticles;
          }
          public function setGroups($groups) {
               $this->groups = $groups;
          }
          public function setAdminGroups($adminGroups) {
               $this->adminGroups = $adminGroups;
          }
          //Parent abstract initWithSQLRow and SQLFields override
          public function initWithSQLRow($SQLRow) {
               $this->id            = isset($SQLRow['id']) ? $SQLRow['id'] : NULL;
               $this->name          = isset($SQLRow['name']) ? $SQLRow['name'] : NULL;
               $this->username      = isset($SQLRow['username']) ? $SQLRow['username'] : NULL;
               $this->authkey       = isset($SQLRow['authkey']) ? $SQLRow['authkey'] : NULL;
               $this->signature     = isset($SQLRow['signature']) ? $SQLRow['signature'] : NULL;
               $this->creation_date = isset($SQLRow['creation_date']) ? $SQLRow['creation_date'] : NULL;
               $this->password      = isset($SQLRow['password']) ? $SQLRow['password'] : NULL;
          }
          public function SQLFields() {
               return array(
                    $this->id,
                    $this->name,
                    $this->username,
                    $this->password,
                    $this->authkey,
                    $this->signature
               );
          }
          
          //Model behaviour
          public function isAdminOfGroup($group) {
               $this->checkAdminGroups();
               foreach ($this->adminGroups as $admin) {
                    if ($admin->id() == $group->id() && !is_null($admin->id())) {
                         return 1;
                    }
               }
               return 0;
          }
          public function isUserOfGroup($group) {
               $this->checkGroups();
               foreach ($this->groups as $user) {
                    if ($user->id() == $group->id() && !is_null($user->id())) {
                         return 1;
                    }
               }
               return 0;
          }
          public function hasPermissionForArticle($article) {
               if ($article->secure() || $article->hidden()) {
                    $this->checkAdminArticles();
                    $this->checkUserArticles();
                    $this->checkGroupArticles();
                    foreach ($this->articles as $article) {
                         if ($article->id() == $article->id()) {
                              return 1;
                         }
                    }
               } else {
                    return 1;
               }
          }
          
          //Private functions
          private function loadGroupArticles($idsOnly = 0) {
               $this->groupArticles = ArticleFactory::LoadGroupArticlesForUser($this, $idsOnly);
          }
          private function loadAdminArticles($idsOnly = 0) {
               $this->adminArticles = ArticleFactory::LoadAdminArticlesForUser($this, $idsOnly);
          }
          private function loadUserArticles($idsOnly = 0) {
               $this->userArticles = ArticleFactory::LoadUserArticlesForUser($this, $idsOnly);
          }
          private function loadGroups($idsOnly = 0) {
               $this->groups = GroupFactory::LoadGroupsForUser($this, $idsOnly);
               
          
               
          }
          private function loadAdminGroups($idsOnly = 0) {
               $this->adminGroups = GroupFactory::LoadAdminGroupsForUser($this, $idsOnly);
          }
          private function checkAdminArticles($idsOnly = 0) {
               if (!isset($this->adminArticles)) {
                    $this->loadAdminArticles($idsOnly);
               }
          }
          private function checkGroupArticles($idsOnly = 0) {
               if (!isset($this->groupArticles)) {
                    $this->loadGroupArticles($idsOnly);
               }
          }
          private function checkUserArticles($idsOnly = 0) {
               if (!isset($this->userArticles)) {
                    $this->loadUserArticles($idsOnly);
               }
          }
          private function checkGroups($idsOnly = 0) {
               if (!isset($this->groups)) {
                    $this->loadGroups($idsOnly);
               }
          }
          private function checkAdminGroups($idsOnly = 0) {
               if (!isset($this->adminGroups)) {
                    $this->loadAdminGroups($idsOnly);
               }
          }
     }
?>