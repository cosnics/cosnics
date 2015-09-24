FILES = [
  {:path => 'soundcloud.php', :search => /(\@version\s)([0-9.]+)/, :replace => "\\1%s"},
  {:path => 'soundcloud.php', :search => /(const VERSION\s=\s\')([0-9.]+)/, :replace => "\\1%s"},
]

namespace :version do
  task :bump, :version do |t, args|
    FILES.each do |file|
      contents = File.read(file[:path]).gsub(file[:search], file[:replace] % [args[:version]])
      File.open(file[:path], 'w') { |f| f.write contents }
    end

    print "Release message? "
    message = STDIN.gets.chomp

    exec "git commit -am 'Bumped version.'"
    exec "git tag -a v#{args[:version]} -m '#{message}'"
  end
end