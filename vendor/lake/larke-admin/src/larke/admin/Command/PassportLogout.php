<?php

declare (strict_types = 1);

namespace Larke\Admin\Command;

use Illuminate\Console\Command;

/**
 * 强制将 jwt 的 refreshToken 放入黑名单
 *
 * php artisan larke-admin:passport-logout
 */
class PassportLogout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'larke-admin:passport-logout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'larke-admin passport-logout';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->logout();
    }
    
    /**
     * logout command.
     *
     * @return mixed
     */
    protected function logout()
    {
        $emptyNum = 0;
        
        askForRefreshToken:
        $refreshToken = $this->ask('Please enter a refreshToken');
        
        if (empty($refreshToken)) {
            $emptyNum ++;
            
            if ($emptyNum < 3) {
                goto askForRefreshToken;
            } else {
                $this->line("<error>The refreshToken what you entered is not empty !</error> ");
                return;
            }
        }
        
        if (app('larke-admin.cache')->has(md5($refreshToken))) {
            $this->line("<error>RefreshToken is logouted !</error> ");

            return;
        }
        
        try {
            $decodeRefreshToken = app('larke-admin.auth-token')
                ->decodeRefreshToken($refreshToken);
            
            // 验证
            app('larke-admin.auth-token')->validate($decodeRefreshToken);
            
            // 签名
            app('larke-admin.auth-token')->verify($decodeRefreshToken);
            
            $refreshAdminid = $decodeRefreshToken->getData('adminid');
            
            // 过期时间
            $refreshTokenExpiresIn = $decodeRefreshToken->getClaim('exp') - $decodeRefreshToken->getClaim('iat');
       } catch(\Exception $e) {
            $this->line("<error>".$e->getMessage()."</error> ");

            return;
        }
        
        // 添加进黑名单
        app('larke-admin.cache')->add(md5($refreshToken), time(), $refreshTokenExpiresIn);
        
        $this->info('Logout success and adminid is: '.$refreshAdminid);
    }
}
