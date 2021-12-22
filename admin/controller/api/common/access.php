<?php
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ControllerApiCommonAccess extends AControllerAPI
{

    public function main()
    {
        //check if any restriction on caller IP
        if (!$this->_validate_ip()) {
            return $this->dispatch('api/error/no_access');
        }
        //validate if API enabled and KEY matches. 
        if ($this->config->get('config_admin_api_status')) {
            if ($this->config->get('config_admin_api_key')
                && ($this->config->get('config_admin_api_key') == $this->request->post['api_key']
                    || $this->config->get('config_admin_api_key') == $this->request->get['api_key'])
            ) {
                return null;
            } else {
                if (!$this->config->get('config_admin_api_key')) {
                    return null;
                }
            }
        }
        return $this->dispatch('api/error/no_access');
    }

    private function _validate_ip()
    {
        if (!has_value($this->config->get('config_admin_access_ip_list'))) {
            return true;
        }

        $ips = array_map('trim', explode(",", $this->config->get('config_admin_access_ip_list')));
        if (in_array($this->request->getRemoteIP(), $ips)) {
            return true;
        }
        return false;
    }

    public function login()
    {
        $request = $this->rest->getRequestParams();
        //allow access to listed controllers with no login
        if (isset($request['rt']) && !isset($request['token'])) {
            $route = '';
            //remove controller type prefixes
            $request['rt'] = ltrim($request['rt'], 'a/');
            $request['rt'] = ltrim($request['rt'], 'r/');
            $request['rt'] = ltrim($request['rt'], 'p/');
            $part = explode('/', $request['rt']);

            if (isset($part[0])) {
                $route .= $part[0];
            }
            if (isset($part[1])) {
                $route .= '/'.$part[1];
            }
            $ignore = [
                'api/index/login',
                'api/common/access',
                'api/error/not_found',
                'api/error/no_access',
                'api/error/no_permission',
            ];

            if (!in_array($route, $ignore)) {
                return $this->dispatch('api/index/login');
            }
        } else {
            if (!$this->user->isLoggedWithToken($request['token'])) {
                return $this->dispatch('api/index/login');
            }
        }
        return null;
    }

    public function permission()
    {
        $request = $this->rest->getRequestParams();

        if ($this->extensions->isExtensionController($request['rt'])) {
            return null;
        }

        if (isset($request['rt'])) {
            $route = '';
            //remove controller type prefixes
            $request['rt'] = ltrim($request['rt'], 'a/');
            $request['rt'] = ltrim($request['rt'], 'r/');
            $request['rt'] = ltrim($request['rt'], 'p/');
            $part = explode('/', $request['rt']);

            if (isset($part[0])) {
                $route .= $part[0];
            }
            if (isset($part[1])) {
                $route .= '/'.$part[1];
            }
            $ignore = [
                'api/index/login',
                'api/common/access',
                'api/error/not_found',
                'api/error/no_access',
                'api/error/no_permission',
            ];

            if (!in_array($route, $ignore)) {
                if (!$this->user->canAccess($route)) {
                    return $this->dispatch('api/error/no_permission');
                }
            }
        }
    }

}



