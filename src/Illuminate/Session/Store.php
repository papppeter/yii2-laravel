<?php
namespace papppeter\yii2Laravel\Illuminate\Session;

use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Arr;
use yii\base\NotSupportedException;

/**
 *
 * @property string $previousUrl
 * @property \Illuminate\Http\Request $requestOnHandler
 */
class Store extends \yii\web\Session implements Session {

    public function put($key, $value = null) {
        $this->set($key, $value);
    }

    /**
     * Start the session, reading the data from a handler.
     *
     * @return bool
     */
    public function start()
    {
        return $this->open();
    }

    /**
     * Save the session data to storage.
     *
     * @return bool
     */
    public function save()
    {
        $this->freeze();
    }

    /**
     * Get all of the session data.
     *
     * @return array
     */
    public function all()
    {
        return $_SESSION ?? [];
    }

    /**
     * Checks if a key exists.
     *
     * @param  string|array $key
     * @return bool
     */
    public function exists($key)
    {
        return ! collect(is_array($key) ? $key : func_get_args())->contains(function ($key) {
            return ! Arr::exists(array_keys($this->all()), $key);
        });
    }

    /**
     * Checks if an a key is present and not null.
     *
     * @param  string|array $key
     * @return bool
     */
    public function has($key)
    {
        return ! collect(is_array($key) ? $key : func_get_args())->contains(function ($key) {
            return is_null($this->get($key));
        });
    }

    /**
     * Get the CSRF token value.
     *
     * @return string
     */
    public function token()
    {
        return $this->get('_token');
    }

    /**
     * Remove one or many items from the session.
     *
     * @param  string|array $keys
     * @return void
     */
    public function forget($keys)
    {
        foreach(array_wrap($keys) as $key) {
            $this->remove($key);
        }
    }

    /**
     * Remove all of the items from the session.
     *
     * @return void
     */
    public function flush()
    {
        $this->removeAll();
    }

    /**
     * Generate a new session ID for the session.
     *
     * @param  bool $destroy
     * @return bool
     */
    public function migrate($destroy = false)
    {
        $this->regenerateID($destroy);

        return true;
    }

    /**
     * Determine if the session has been started.
     *
     * @return bool
     */
    public function isStarted()
    {
        return $this->getIsActive();
    }

    /**
     * Get the previous URL from the session.
     *
     * @return string|null
     */
    public function previousUrl()
    {
        return $this->get('_previous.url');
    }

    /**
     * Set the "previous" URL in the session.
     *
     * @param  string $url
     * @return void
     */
    public function setPreviousUrl($url)
    {
        $this->put('_previous.url', $url);
    }

    /**
     * Get the session handler instance.
     *
     * @return \SessionHandlerInterface
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * Determine if the session handler needs a request.
     *
     * @return bool
     */
    public function handlerNeedsRequest()
    {
        return false;
    }

    /**
     * Set the request on the handler instance.
     *
     * @param  \Illuminate\Http\Request $request
     * @return void
     */
    public function setRequestOnHandler($request)
    {
        if ($this->handlerNeedsRequest()) {
            $this->handler->setRequest($request);
        }
    }

    public function pull($key, $default = null)
    {
        $value = $this->get($key, $default);
        $this->offsetUnset($key);
        return $value;
    }

    public function regenerateToken()
    {
        throw new NotSupportedException("pull is not supported.");
    }

    public function invalidate()
    {
        $success = $this->getIsActive();
        $this->destroy();
        return $success;
    }

    public function regenerate($destroy = false)
    {
        $this->regenerateID($destroy);
        return $this->getId();
    }
}