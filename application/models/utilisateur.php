<?php

class Utilisateur extends CustomEloquent {

    // White-list of accessible columns by a mass-assignement !
    public static $accessible = array('login', 'mail', 'prenom', 'nom', 'departement_id','promo', 'ardoise_id');

    public static $rules = array(
        'login'          => 'required|alpha_num|exists:utilisateur,login',
        'mail'           => 'required|email',
        'mdp'            => 'required',
        'prenom'         => 'required',
        'nom'            => 'required',
        'departement_id' => 'required|exists:departement,id',
        'promo'          => 'required|numeric',
        'ardoise_id'     => 'exists:ardoise,id'
    );

    public function ardoise()
    {
        return $this->belongs_to('Ardoise');
    }
    
    public function departement()
    {
        return Departement::find($this->departement_id);
    }
    
    /*public function consommations()
    {
        return $this->ardoise->consommations;
    }*/

    public function roles()
    {
        return $this->has_many_and_belongs_to('Role', 'utilisateur_role')->with('id');
    }
    
    public function roles_id()
    {
        return array_map(function($r){return $r->id;},$this->roles);
    }
    
    public function permissions()
    {
        $perms = array();
        foreach($this->roles()->get() as $r)
        {
            foreach($r->permissions as $p)
            {
                $perms[$p->nom] = true;
            }
        }
        return $perms;
    }
    
    public function role()
    {
        return $this->belongs_to('Role');
    }
    
    
    public function has_role($key)
    {
        foreach(Auth::user()->roles as $role)
        {
            if($role->name == $key)
              return true;
      }
      return false;
  }

  public function has_any_role($keys)
  {
    if( ! is_array($keys))
        $keys = func_get_args();

    foreach(Auth::user()->roles as $role)
    {
        if(in_array($role->name, $keys))
            return true;
    }
    return false;
}


    /**
     * Can the User do something
     * 
     * @param  array|string $permissions Single permission or an array or permissions
     * @return boolean
     */
    public function can($permissions)
    {       
        $permissions = !is_array($permissions)
        ? array($permissions)
        : $permissions;

        $class = get_class();
        $to_check = new $class;

        $to_check = $class::with(array('role', 'role.permissions'))
        ->where('id', '=', $this->get_attribute('id'))
        ->first();
        
        $valid = FALSE;
        
        
        foreach ($to_check->roles as $role)
        {
            foreach($role->permissions as $permission)
            {               
                if (in_array($permission->nom, $permissions))
                {
                    $valid = TRUE;
                    break;
                }
            }
        }

        return $valid;
    }

    /**
     * Is the User a Role
     * 
     * @param  array|string  $roles A single role or an array of roles
     * @return boolean
     */
    public function is($roles)
    {
        $roles = !is_array($roles) ? array($roles) : $roles;
        $valid = FALSE;

        foreach ($roles as $role)
        {
            foreach ($this->roles as $r)
            {
                if ($r->nom === $role)
                {
                    $valid = TRUE;
                    break;
                }
            }
        }

        return $valid;
    }
}
